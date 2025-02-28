<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\MinuteBundle\Utils;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier;
use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class CourierManager
 *
 * @package Lucca\MinuteBundle\Utils
 * @author Térence <terence@numeric-wave.tech>
 */
class CourierManager
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
     * @var CourierEditionManager
     */
    private $courierEditionManager;

    /**
     * CourierManager constructor
     *
     * @param EntityManager $entityManager
     * @param Session $session
     * @param CourierEditionManager $courierEditionManager
     */
    public function __construct(EntityManager $entityManager, Session $session, CourierEditionManager $courierEditionManager)
    {
        $this->em = $entityManager;
        $this->session = $session;
        $this->courierEditionManager = $courierEditionManager;
    }

    /**
     * Create Courier after Folder fence
     *
     * @param Folder $p_folder
     * @return Courier|object|null
     */
    public function createCourierToFolder(Folder $p_folder)
    {
        $courier = $this->em->getRepository('LuccaMinuteBundle:Courier')->findOneBy(array(
            'folder' => $p_folder
        ));

        if (!$courier) {
            $courier = new Courier();
            $courier->setFolder($p_folder);
        }

        /** Create / update / delete editions if needed */
        $this->courierEditionManager->manageEditionsOnFormSubmission($courier);
        $this->session->getFlashBag()->add('info', 'flash.courier.addSuccessfully');

        $this->em->persist($courier);

        return $courier;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.manager.courier';
    }
}
