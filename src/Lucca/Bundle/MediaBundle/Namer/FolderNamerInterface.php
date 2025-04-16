<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Namer;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\MediaBundle\Entity\Media;

interface FolderNamerInterface
{
    /**
     * Service constant to Folder
     * CAREFUL -- All of these name is declared in service.yml in MediaBundle (not anymore)
     */
    const NAMER_FOLDER = 'lucca.namer.folder';
    const NAMER_FOLDER_BY_DATE = 'lucca.namer.folder_by_date';

    /**
     * Search a Folder Entity and manage these Folder
     * Each service can have a different logic
     * If Folder does not exist - Create a new Folder Entity and new filesystem
     */
    public function searchFolder(Media $media): mixed;
}
