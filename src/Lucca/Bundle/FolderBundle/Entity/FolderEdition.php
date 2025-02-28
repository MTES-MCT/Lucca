<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FolderEdition
 *
 * @ORM\Table(name="lucca_minute_folder_edition")
 * @ORM\Entity()
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class FolderEdition implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="folderEdited", type="boolean")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $folderEdited = false;

    /**
     * @var string
     *
     * @ORM\Column(name="folderVersion", type="text", nullable=true)
     */
    private $folderVersion;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Procès verbal édition';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set folderEdited
     *
     * @param boolean $folderEdited
     *
     * @return FolderEdition
     */
    public function setFolderEdited($folderEdited)
    {
        $this->folderEdited = $folderEdited;

        return $this;
    }

    /**
     * Get folderEdited
     *
     * @return boolean
     */
    public function getFolderEdited()
    {
        return $this->folderEdited;
    }

    /**
     * Set folderVersion
     *
     * @param string $folderVersion
     *
     * @return FolderEdition
     */
    public function setFolderVersion($folderVersion)
    {
        $this->folderVersion = $folderVersion;

        return $this;
    }

    /**
     * Get folderVersion
     *
     * @return string
     */
    public function getFolderVersion()
    {
        return $this->folderVersion;
    }
}
