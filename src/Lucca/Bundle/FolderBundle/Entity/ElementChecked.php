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
use Lucca\ChecklistBundle\Entity\Element;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Lucca\MediaBundle\Entity\Media;
use Lucca\MediaBundle\Entity\MediaAsyncInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ElementChecked
 *
 * @ORM\Table(name="lucca_minute_folder_element")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\ElementCheckedRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class ElementChecked implements LogInterface, MediaAsyncInterface
{
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
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Folder", inversedBy="elements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $folder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 255,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="state", type="boolean")
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $state = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="smallint", nullable=true)
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * ElementChecked constructor.
     *
     * @param Folder|null $folder
     * @param Element|null $element
     */
    public function __construct(Folder $folder = null, Element $element = null)
    {
        if ($folder)
            $this->setFolder($folder);

        if ($element)
            $this->setName($element->getName());
    }

    /**
     * Set media by asynchronous method.
     *
     * @param Media|null $media
     *
     * @return ElementChecked
     */
    public function setAsyncMedia(Media $media = null)
    {
        $this->setImage($media);

        return $this;
    }

    /**
     * Get media by asynchronous method.
     *
     * @return Media|null
     */
    public function getAsyncMedia()
    {
        return $this->getImage();
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Element validé';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ElementChecked
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set state.
     *
     * @param bool $state
     *
     * @return ElementChecked
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return bool
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set position.
     *
     * @param int $position
     *
     * @return ElementChecked
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return ElementChecked
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set folder.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder $folder
     *
     * @return ElementChecked
     */
    public function setFolder(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder $folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder.
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set image.
     *
     * @param \Lucca\MediaBundle\Entity\Media $image
     *
     * @return ElementChecked
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return \Lucca\MediaBundle\Entity\Media
     */
    public function getImage()
    {
        return $this->image;
    }
}
