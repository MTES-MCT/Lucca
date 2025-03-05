<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Utils;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\EntityManagerInterface;

use Lucca\Bundle\FolderBundle\Entity\{Folder, FolderEdition};

readonly class FolderEditionManager
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    public function manageEditionsOnFormSubmission(Folder $folder): FolderEdition
    {
        if (!$folder->getEdition()) {
            $edition = new FolderEdition();
            $folder->setEdition($edition);
        } else {
            $edition = $folder->getEdition();
        }

        return $edition;
    }

    /**
     * Purge empty edition on Folder entity
     */
    public function purgeEdition(Folder $folder): Folder
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

    public function getName(): string
    {
        return 'lucca.manager.folder_edition';
    }
}
