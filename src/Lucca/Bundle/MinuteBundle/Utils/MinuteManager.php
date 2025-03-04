<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Utils;

use Lucca\Bundle\MinuteBundle\Entity\Human;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Doctrine\ORM\EntityManager;
use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class MinuteManager
 *
 * @package Lucca\Bundle\MinuteBundle\Utils
 * @author Térence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class MinuteManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var AdherentFinder
     */
    private $adherentFinder;

    /**
     * MinuteManager constructor
     *
     * @param EntityManager $entityManager
     * @param Session $session
     * @param AdherentFinder $p_adherentFinder
     */
    public function __construct(EntityManager $entityManager, Session $session, AdherentFinder $p_adherentFinder)
    {
        $this->em = $entityManager;
        $this->session = $session;
        $this->adherentFinder = $p_adherentFinder;
    }

    /**
     * TODO : this need to be improve.
     * Minute request explode if there is to many result so we have to check filter date form to control it in first place.
     * issue tag : https://gitlab.numeric-wave.tech/nw/lucca/-/issues/128
     *
     * @param $p_filters
     * @return bool
     */
    public function checkFilters($p_filters)
    {
        // If there is an other filter selected, it's ok
        // Be careful we need to check if array key exist because if the user is not admin not all of these fields are defined
        if ($p_filters['num'] != null || count($p_filters['status']) != 0
            || (array_key_exists('adherent', $p_filters) && $p_filters['adherent'] != null && count($p_filters['adherent']) != 0)
            || (array_key_exists('service', $p_filters) && $p_filters['service'] != null && count($p_filters['service']) != 0)
            || (array_key_exists('adherent_intercommunal', $p_filters) && $p_filters['adherent_intercommunal'] != null && count($p_filters['adherent_intercommunal']) != 0)
            || (array_key_exists('adherent_town', $p_filters) && $p_filters['adherent_town'] != null && count($p_filters['adherent_town']) != 0)
            || (array_key_exists('folder_intercommunal', $p_filters) && $p_filters['folder_intercommunal'] != null && count($p_filters['folder_intercommunal']) != 0)
            || (array_key_exists('folder_town', $p_filters) && $p_filters['folder_town'] != null && count($p_filters['folder_town']) != 0)) {
            return true;
        }

        // If interval between date is bigger than 1 year -> false
        $interval = date_diff($p_filters['dateStart'], $p_filters['dateEnd']);
        if (intval($interval->format('%a')) > 365) {
            return false;
        }
        return true;
    }

    /**
     * Check Minute entity
     *
     * @param Minute $p_minute
     * @return bool
     */
    public function checkMinute(Minute $p_minute)
    {
        /** @var Human $human - Check each Human linked to this Minute */
        foreach ($p_minute->getHumans() as $human) {
            if (!$human->getName())
                $this->session->getFlashBag()->add('danger', 'flash.human.nameIsRequired');
            if (!$human->getFirstname())
                $this->session->getFlashBag()->add('danger', 'flash.human.firstnameIsRequired');
        }

        /** Check if an dangerous message has been thrown by test */
        if ($this->session->getFlashBag()->has('danger'))
            return false;

        return true;
    }

    /**
     * Check if the adherent can access to the minute
     *
     * @param Minute $p_minute
     * @param Adherent $p_adherent
     * @return bool
     */
    public function checkAccessMinute(Minute $p_minute, Adherent $p_adherent)
    {
        if ($p_minute->getAdherent() !== $p_adherent
            && $p_minute->getPlot()->getTown() !== $p_adherent->getTown()
            && $p_minute->getPlot()->getTown()->getIntercommunal() !== $p_adherent->getIntercommunal()
            && $p_adherent->getFunction() != Adherent::FUNCTION_COUNTRY_AGENT
            && $p_adherent->getService() == null) {
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
     *
     * @param Minute $p_minute
     * @param false $p_isCommand
     */
    public function updateStatusAction(Minute $p_minute, bool $p_isCommand = false)
    {
        /** init steps */
        $controls = $p_minute->getControls();

        $folder = $this->em->getRepository(Folder')->findLastByMinute($p_minute, $p_isCommand);

        $couriers = array();
        $updates = $p_minute->getUpdatings();
        $decisions = $p_minute->getDecisions();
        $closure = $p_minute->getClosure();

        $status = 1;
        $date = '';

        /** Check if folder exist for this minute */
        if ($controls != null && count($controls) > 0) {
            $status = 2;
        }

        /** Check if folder exist for this minute */
        if ($folder != null) {
            $couriers = $this->em->getRepository(Courier')->findCouriersByFolder($folder[0]);
            $status = 3;
        }

        switch (true) {
            case ($closure == !null):
                $p_minute->setStatus(Minute::STATUS_CLOSURE);
                if ($p_minute->getClosure()->getDateClosing() !== null) {
                    $date = $p_minute->getClosure()->getDateClosing();
                } elseif ($p_minute->getClosure()->getUpdatedAt() !== null) {
                    $date = $p_minute->getClosure()->getUpdatedAt();
                } else {
                    $date = $p_minute->getClosure()->getCreatedAt();
                }
                break;
            case (count($decisions) > 0):
                $p_minute->setStatus(Minute::STATUS_DECISION);
                foreach ($decisions as $decision) {
                    // if updatedAt is not null, date = updatedAt, else date = createdAt
                    $date = ($decision->getUpdatedAt() !== null) ? $decision->getUpdatedAt() : $decision->getCreatedAt();
                }
                break;
            case (count($updates) > 0):
                $p_minute->setStatus(Minute::STATUS_UPDATING);
                foreach ($updates as $update) {
                    $date = ($update->getUpdatedAt() !== null) ? $update->getUpdatedAt() : $update->getCreatedAt();
                }
                break;
            case (count($couriers) > 0):
                $p_minute->setStatus(Minute::STATUS_COURIER);
                foreach ($couriers as $courier) {
                    $date = ($courier->getUpdatedAt() !== null) ? $courier->getUpdatedAt() : $courier->getCreatedAt();
                }
                break;
            case ($status === 3 && $folder != null):
                $p_minute->setStatus(Minute::STATUS_FOLDER);
                $date = ($folder[0]->getUpdatedAt() !== null) ? $folder[0]->getUpdatedAt() : $folder[0]->getCreatedAt();
                break;
            case ($status === 2):
                $p_minute->setStatus(Minute::STATUS_CONTROL);
                foreach ($controls as $control) {
                    $date = ($control->getUpdatedAt() !== null) ? $control->getUpdatedAt() : $control->getCreatedAt();
                }
                break;
            case ($status === 1):
                $p_minute->setStatus(Minute::STATUS_OPEN);
                if ($p_minute->getDateOpening() !== null) {
                    $date = $p_minute->getDateOpening();
                } else {
                    if ($p_minute->getUpdatedAt() == null) {
                        $date = $p_minute->getCreatedAt();
                    }
                }
                break;
        }

        if ($date !== '')
            $p_minute->setDateLastUpdate($date);
        else
            $p_minute->setDateLastUpdate($p_minute->getCreatedAt());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.manager.minute';
    }
}
