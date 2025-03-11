<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Manager;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\FolderBundle\Entity\{Courier, Folder};
use Lucca\Bundle\MinuteBundle\Entity\{Human, Minute};

readonly class MinuteManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack           $requestStack,
    )
    {
    }

    /**
     * TODO : this need to be improve.
     * Minute request explode if there is to many result so we have to check filter date form to control it in first place.
     * issue tag : https://gitlab.numeric-wave.tech/nw/lucca/-/issues/128
     */
    public function checkFilters($filters): bool
    {
        // If there is an other filter selected, it's ok
        // Be careful we need to check if array key exist because if the user is not admin not all of these fields are defined
        if ($filters['num'] != null || count($filters['status']) != 0
            || (array_key_exists('adherent', $filters) && $filters['adherent'] != null && count($filters['adherent']) != 0)
            || (array_key_exists('service', $filters) && $filters['service'] != null && count($filters['service']) != 0)
            || (array_key_exists('adherent_intercommunal', $filters) && $filters['adherent_intercommunal'] != null && count($filters['adherent_intercommunal']) != 0)
            || (array_key_exists('adherent_town', $filters) && $filters['adherent_town'] != null && count($filters['adherent_town']) != 0)
            || (array_key_exists('folder_intercommunal', $filters) && $filters['folder_intercommunal'] != null && count($filters['folder_intercommunal']) != 0)
            || (array_key_exists('folder_town', $filters) && $filters['folder_town'] != null && count($filters['folder_town']) != 0)) {
            return true;
        }

        // If interval between date is bigger than 1 year -> false
        $interval = date_diff($filters['dateStart'], $filters['dateEnd']);
        if (intval($interval->format('%a')) > 365) {
            return false;
        }

        return true;
    }

    /**
     * Check Minute entity
     */
    public function checkMinute(Minute $minute): bool
    {
        /** @var Human $human - Check each Human linked to this Minute */
        foreach ($minute->getHumans() as $human) {
            if (!$human->getName()) {
                $this->requestStack->getSession()->getFlashBag()->add('danger', 'flash.human.nameIsRequired');
            }

            if (!$human->getFirstname()) {
                $this->requestStack->getSession()->getFlashBag()->add('danger', 'flash.human.firstnameIsRequired');
            }
        }

        /** Check if an dangerous message has been thrown by test */
        if ($this->requestStack->getSession()->getFlashBag()->has('danger')) {
            return false;
        }

        return true;
    }

    /**
     * Check if the adherent can access to the minute
     */
    public function checkAccessMinute(Minute $minute, Adherent $adherent): bool
    {
        if ($minute->getAdherent() !== $adherent
            && $minute->getPlot()->getTown() !== $adherent->getTown()
            && $minute->getPlot()->getTown()->getIntercommunal() !== $adherent->getIntercommunal()
            && $adherent->getFunction() != Adherent::FUNCTION_COUNTRY_AGENT
            && $adherent->getService() == null) {
            return false;
        }

        return true;
    }

    /**
     * Check and update Minute status
     *
     * This function is called in the showAction() in MinuteController.
     * TODO: we should maybe optimize this ->
     *      - call this function every time in show can be heavy if  lot of user are connected
     *      - the foreach in loop can be heavy
     */
    public function updateStatusAction(Minute $minute, bool $isCommand = false): void
    {
        /** init steps */
        $controls = $minute->getControls();

        $folder = $this->em->getRepository(Folder::class)->findLastByMinute($minute, $isCommand);

        $couriers = [];
        $updates = $minute->getUpdatings();
        $decisions = $minute->getDecisions();
        $closure = $minute->getClosure();

        $status = 1;
        $date = '';

        /** Check if folder exist for this minute */
        if ($controls != null && count($controls) > 0) {
            $status = 2;
        }

        /** Check if folder exist for this minute */
        if ($folder != null) {
            $couriers = $this->em->getRepository(Courier::class)->findCouriersByFolder($folder[0]);
            $status = 3;
        }

        switch (true) {
            case ($closure == !null):
                $minute->setStatus(Minute::STATUS_CLOSURE);
                if ($minute->getClosure()->getDateClosing() !== null) {
                    $date = $minute->getClosure()->getDateClosing();
                } elseif ($minute->getClosure()->getUpdatedAt() !== null) {
                    $date = $minute->getClosure()->getUpdatedAt();
                } else {
                    $date = $minute->getClosure()->getCreatedAt();
                }
                break;
            case (count($decisions) > 0):
                $minute->setStatus(Minute::STATUS_DECISION);
                foreach ($decisions as $decision) {
                    // if updatedAt is not null, date = updatedAt, else date = createdAt
                    $date = ($decision->getUpdatedAt() !== null) ? $decision->getUpdatedAt() : $decision->getCreatedAt();
                }
                break;
            case (count($updates) > 0):
                $minute->setStatus(Minute::STATUS_UPDATING);
                foreach ($updates as $update) {
                    $date = ($update->getUpdatedAt() !== null) ? $update->getUpdatedAt() : $update->getCreatedAt();
                }
                break;
            case (count($couriers) > 0):
                $minute->setStatus(Minute::STATUS_COURIER);
                foreach ($couriers as $courier) {
                    $date = ($courier->getUpdatedAt() !== null) ? $courier->getUpdatedAt() : $courier->getCreatedAt();
                }
                break;
            case ($status === 3 && $folder != null):
                $minute->setStatus(Minute::STATUS_FOLDER);
                $date = ($folder[0]->getUpdatedAt() !== null) ? $folder[0]->getUpdatedAt() : $folder[0]->getCreatedAt();
                break;
            case ($status === 2):
                $minute->setStatus(Minute::STATUS_CONTROL);
                foreach ($controls as $control) {
                    $date = ($control->getUpdatedAt() !== null) ? $control->getUpdatedAt() : $control->getCreatedAt();
                }
                break;
            case ($status === 1):
                $minute->setStatus(Minute::STATUS_OPEN);
                if ($minute->getDateOpening() !== null) {
                    $date = $minute->getDateOpening();
                } else {
                    if ($minute->getUpdatedAt() == null) {
                        $date = $minute->getCreatedAt();
                    }
                }
                break;
        }

        if ($date !== '') {
            $minute->setDateLastUpdate($date);
        } else {
            $minute->setDateLastUpdate(DateTime::createFromImmutable($minute->getCreatedAt()));
        }
    }

    public function getName(): string
    {
        return 'lucca.manager.minute';
    }
}
