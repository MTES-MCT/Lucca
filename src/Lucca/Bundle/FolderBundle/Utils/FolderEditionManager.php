<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Utils;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\FolderBundle\Entity\FolderEdition;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;

/**
 * Class FolderEditionManager
 *
 * @package Lucca\Bundle\MinuteBundle\Utils
 * @author Térence <terence@numeric-wave.tech>
 */
class FolderEditionManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * FolderEditionManager constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param Folder $folder
     * @return FolderEdition
     */
    public function manageEditionsOnFormSubmission(Folder $folder)
    {
        if (!$folder->getEdition()) {
            $edition = new FolderEdition();
            $folder->setEdition($edition);
        } else
            $edition = $folder->getEdition();

        return $edition;
    }

    /**
     * Purge empty edition on Folder entity
     *
     * @param Folder $folder
     * @return Folder $folder
     */
    public function purgeEdition(Folder $folder)
    {
        $edition = $folder->getEdition();
        /**  If no customization on Edition - delete it */
        if ($edition && !$edition->getFolderEdited()) {
            $folder->setEdition(null);

            try {
                $this->em->remove($edition);
            } catch (ORMException $ORMException) {
                echo 'New exception thrown when remove Folder Edition - ' . $ORMException->getMessage();
            }
        }

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.manager.folder_edition';
    }
}
