<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\FolderBundle\Entity\MayorLetter;

readonly class MayorLetterManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface    $translator,
    )
    {
    }

    /**
     * Check all field it's not null
     */
    public function checkMayorLetterField($data, ?MayorLetter $mayorLetter = null): string
    {
        $message = "";

        /* Check all field is not null */
        if ( $mayorLetter == null && (
            !$data->gender || !$data->nameMayor || !$data->addressMayor || !$data->town || count($data->folders) == 0 )
        ) {
            $message = $this->translator->trans("text.mayorLetter.badField", [], 'LuccaMinuteBundle');
        }

        return $message;
    }

    /**
     * Delete a Mayor Letter
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteMayorLetter(MayorLetter $mayorLetter): void
    {
        $this->em->remove($mayorLetter);
        $this->em->flush();
    }

    public function getName(): string
    {
        return 'lucca.manager.mayor.letter';
    }
}
