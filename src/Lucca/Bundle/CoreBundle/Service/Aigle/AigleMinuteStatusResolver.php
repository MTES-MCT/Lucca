<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Service\Aigle;

use Lucca\Bundle\MinuteBundle\Entity\Closure;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\Minute;

readonly class AigleMinuteStatusResolver
{

//    const STATUS_OPEN = 'open';
//    const STATUS_CONTROL = 'control';
//
//    const STATUS_FOLDER = 'folder';
//    const STATUS_REPORT = 'report';
//    const STATUS_AIT = 'ait';
//    const STATUS_UPDATING = 'updating';
//    const STATUS_CLOSURE = 'closure';
//    const STATUS_REGULARISED = 'regularised';


    //not match in lucca
    const STATUS_PRIOR_LETTER_SENT = 'PRIOR_LETTER_SENT'; // TODO: Ne pas utiliser quand le status est STATUS_COURIER ??
    const STATUS_ADMINISTRATIVE_CONSTRAINT = 'ADMINISTRATIVE_CONSTRAINT';

    //implemented and match in lucca
    const STATUS_NOT_CONTROLLED = 'NOT_CONTROLLED';
    const STATUS_CONTROLLED_FIELD = 'CONTROLLED_FIELD';

    const STATUS_OFFICIAL_REPORT_DRAWN_UP = 'OFFICIAL_REPORT_DRAWN_UP';
    const STATUS_OBSERVARTION_REPORT_REDACTED = 'OBSERVARTION_REPORT_REDACTED';
    const STATUS_REHABILITATED = 'REHABILITATED';

    //not use in aigle but anyway sent
    const STATUS_UPDATING = 'updating';
    const STATUS_CLOSURE = 'closure';

    //not use in aigle
    const STATUS_AIT = 'ait';

    public function statusResolver(Minute $minute): ?string
    {
        return match ($minute->getStatus()) {
            Minute::STATUS_OPEN => self::STATUS_NOT_CONTROLLED,
            Minute::STATUS_CONTROL => self::STATUS_CONTROLLED_FIELD,
            Minute::STATUS_FOLDER => self::STATUS_OFFICIAL_REPORT_DRAWN_UP,
//            Minute::STATUS_COURIER => self::STATUS_REPORT, //TODO: a check car pas de status report
            Minute::STATUS_UPDATING => $this->updatingStatusResolver($minute),
            Minute::STATUS_CLOSURE => $this->clotureStatusResolver($minute),
            //Minute::STATUS_DECISION => self::STATUS_REGULARISED, -> TODO: a check car pas de status regularised dans lucca pour Minute
            default => null,
        };
    }

    private function clotureStatusResolver(Minute $minute): string
    {

        if ($minute->getClosure()?->getStatus() === Closure::STATUS_REGULARIZED) {
            return self::STATUS_REHABILITATED;
        }

        return self::STATUS_CLOSURE;
    }

    // Determine if the status should be OBSERVARTION_REPORT_REDACTED or UPDATING
    // based on the presence of Natinfs in the last folder's controls
    // If there are no Natinfs, return OBSERVARTION_REPORT_REDACTED
    // Otherwise, return UPDATING
    private function updatingStatusResolver(Minute $minute): string
    {
        $lastFolder = null;
        /** @var Control $control */
        foreach ($minute->getControls() as $control) {
            $folder = $control->getFolder();


            if ($folder === null) {
                continue;
            }

            /** Keep only folders that have a date of closure */
            if ($folder->getDateClosure() === null) {
                continue;
            }

            if (
                $lastFolder === null ||
                $folder->getCreatedAt() > $lastFolder->getCreatedAt()
            ) {
                $lastFolder = $folder;
            }
        }

        if ($lastFolder?->getNatinfs()?->isEmpty()) {
            return self::STATUS_OBSERVARTION_REPORT_REDACTED;
        }

        return self::STATUS_UPDATING;
    }
}