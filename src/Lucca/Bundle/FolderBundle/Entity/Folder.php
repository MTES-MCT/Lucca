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

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Lucca\MediaBundle\Entity\Media;
use Lucca\MediaBundle\Entity\MediaAsyncInterface;
use Lucca\MediaBundle\Entity\MediaListAsyncInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Folder
 *
 * @ORM\Table(name="lucca_minute_folder")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\FolderRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class Folder implements LogInterface, MediaAsyncInterface, MediaListAsyncInterface
{
    /** Traits */
    use TimestampableTrait;

    /** TYPE constants */
    const TYPE_FOLDER = 'choice.type.folder';
    const TYPE_REFRESH = 'choice.type.refresh';
    /** NATURE constants */
    const NATURE_HUT = 'choice.nature.hut';
    const NATURE_OTHER = 'choice.nature.other';
    const NATURE_OBSTACLE = 'choice.nature.obstacle';
    const NATURE_FORMAL_OFFENSE = 'choice.nature.formalOffense';
    const NATURE_SUBSTANTIVE_OFFENSE = 'choice.nature.substantiveOffense';
    /** REASON OBSTACLE constants */
    const REASON_OBS_REFUSE_ACCESS_AFTER_LETTER = 'choice.reason_obs.refuseAccessAfterLetter';
    const REASON_OBS_REFUSE_BY_RECIPIENT = 'choice.reason_obs.refuseByRecipient';
    const REASON_OBS_UNCLAIMED_BY_RECIPIENT = 'choice.reason_obs.unclaimedByRecipient';
    const REASON_OBS_ACCESS_REFUSED = 'choice.reason_obs.accessRefused';
    const REASON_OBS_ABSENT_DURING_CONTROL = 'choice.reason_obs.absentDuringControl';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="num", type="string", length=25, unique=true)
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $num;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Minute")
     * @ORM\JoinColumn(nullable=false)
     */
    private $minute;

    /**
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Control", mappedBy="folder")
     * @ORM\JoinColumn(nullable=false)
     */
    private $control;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Natinf")
     * @ORM\JoinTable(name="lucca_minute_folder_linked_natinf",
     *      joinColumns={@ORM\JoinColumn(name="folder_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="natinf_id", referencedColumnName="id")}
     * )
     */
    private $natinfs;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Tag")
     * @ORM\JoinTable(name="lucca_minute_folder_linked_tag_nature",
     *      joinColumns={@ORM\JoinColumn(name="folder_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    private $tagsNature;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Tag")
     * @ORM\JoinTable(name="lucca_minute_folder_linked_tag_town",
     *      joinColumns={@ORM\JoinColumn(name="folder_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    private $tagsTown;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Human")
     * @ORM\JoinTable(name="lucca_minute_folder_linked_human_minute",
     *      joinColumns={@ORM\JoinColumn(name="folder_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="human_id", referencedColumnName="id")}
     * )
     */
    private $humansByMinute;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Human", cascade={"persist"})
     * @ORM\JoinTable(name="lucca_minute_folder_linked_human_folder",
     *      joinColumns={@ORM\JoinColumn(name="folder_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="human_id", referencedColumnName="id")}
     * )
     */
    private $humansByFolder;

    /**
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Courier", inversedBy="folder", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $courier;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=30, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="nature", type="string", length=100, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $nature;

    /**
     * @var string
     *
     * @ORM\Column(name="reasonObstacle", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $reasonObstacle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateClosure", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateClosure;

    /**
     * @var string
     *
     * @ORM\Column(name="ascertainment", type="text", nullable=true)
     */
    private $ascertainment;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="text", nullable=true)
     */
    private $details;

    /**
     * @var string
     *
     * @ORM\Column(name="violation", type="text", nullable=true)
     */
    private $violation;

    /**
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\FolderEdition", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $edition;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Lucca\MinuteBundle\Entity\ElementChecked", mappedBy="folder",
     *     cascade={"persist", "remove"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $elements;

    /**
     * @var bool
     *
     * @ORM\Column(name="isReReaded", type="boolean")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $isReReaded;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $folderSigned;

    /**
     * Many Folder entity can be associated to Many Media
     * @ORM\ManyToMany(targetEntity="Lucca\MediaBundle\Entity\Media", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="lucca_folder_linked_media",
     *      joinColumns={@ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id")}
     * )
     */
    private $annexes;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Folder constructor
     */
    public function __construct()
    {
        $this->natinfs = new ArrayCollection();
        $this->tagsNature = new ArrayCollection();
        $this->tagsTown = new ArrayCollection();

        $this->humansByFolder = new ArrayCollection();
        $this->humansByMinute = new ArrayCollection();
        $this->elements = new ArrayCollection();
        $this->isReReaded = false;
        $this->annexes = new ArrayCollection();
    }

    /**
     * Add element
     * Override
     *
     * @param ElementChecked $element
     * @return Folder
     */
    public function addElement(ElementChecked $element)
    {
        $this->elements[] = $element;
        $element->setFolder($this);

        return $this;
    }

    /**
     * Set control
     *
     * @param Control $control
     *
     * @return Folder
     */
    public function setControl(Control $control)
    {
        $this->control = $control;
        $control->setFolder($this);

        return $this;
    }

    /**
     * Has tag in tags collection
     *
     * @param $string
     * @return bool
     */
    public function hasTag($string)
    {
        foreach ($this->tagsNature as $element) {
            if ($element->getName() === $string)
                return true;
        }
        foreach ($this->getTagsTown() as $element) {
            if ($element->getName() === $string)
                return true;
        }

        return false;
    }

    /**
     * Has natinf in natinfs collection
     * TODO Care string param and natinf num is an int
     *
     * @param $string
     * @return bool
     */
    public function hasNatinf($string)
    {
        foreach ($this->getNatinfs() as $element) {
            if ($element->getNum() == $string)
                return true;
        }

        return false;
    }

    /**
     * Set folderSigned.
     *
     * @param Media|null $folderSigned
     *
     * @return Folder
     */
    public function setFolderSigned(Media $folderSigned = null)
    {
        $this->folderSigned = $folderSigned;

        return $this;
    }

    /**
     * Set media by asynchronous method.
     *
     * @param Media|null $media
     *
     * @return Folder
     */
    public function setAsyncMedia(Media $media = null)
    {
        $this->setFolderSigned($media);

        return $this;
    }

    /**
     * Get media by asynchronous method.
     *
     * @return Media|null
     */
    public function getAsyncMedia()
    {
        return $this->getFolderSigned();
    }

    /**
     * Add media by asynchronous method.
     *
     * @param Media $media
     * @param string|null $vars
     * @return void
     */
    public function addAsyncMedia(Media $media, string $vars = null)
    {
        $this->addAnnex($media);
    }

    /**
     * Remove media by asynchronous method.
     *
     * @param Media $media
     * @param string $vars
     *
     * @return boolean
     */
    /**
     * @param Media $media
     * @param string|null $vars
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAsyncMedia(Media $media, string $vars = null)
    {
        return $this->removeAnnex($media);
    }

    /**
     * Get medias by asynchronous method.
     *
     * @return Collection
     */
    public function getAsyncMedias()
    {
        return $this->getAnnexes();
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Procès verbal';
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
     * Set num.
     *
     * @param string $num
     *
     * @return Folder
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num.
     *
     * @return string
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set type.
     *
     * @param string|null $type
     *
     * @return Folder
     */
    public function setType($type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set nature.
     *
     * @param string|null $nature
     *
     * @return Folder
     */
    public function setNature($nature = null)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get nature.
     *
     * @return string|null
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set reasonObstacle.
     *
     * @param string|null $reasonObstacle
     *
     * @return Folder
     */
    public function setReasonObstacle($reasonObstacle = null)
    {
        $this->reasonObstacle = $reasonObstacle;

        return $this;
    }

    /**
     * Get reasonObstacle.
     *
     * @return string|null
     */
    public function getReasonObstacle()
    {
        return $this->reasonObstacle;
    }

    /**
     * Set dateClosure.
     *
     * @param \DateTime|null $dateClosure
     *
     * @return Folder
     */
    public function setDateClosure($dateClosure = null)
    {
        $this->dateClosure = $dateClosure;

        return $this;
    }

    /**
     * Get dateClosure.
     *
     * @return \DateTime|null
     */
    public function getDateClosure()
    {
        return $this->dateClosure;
    }

    /**
     * Set ascertainment.
     *
     * @param string|null $ascertainment
     *
     * @return Folder
     */
    public function setAscertainment($ascertainment = null)
    {
        $this->ascertainment = $ascertainment;

        return $this;
    }

    /**
     * Get ascertainment.
     *
     * @return string|null
     */
    public function getAscertainment()
    {
        return $this->ascertainment;
    }

    /**
     * Set details.
     *
     * @param string|null $details
     *
     * @return Folder
     */
    public function setDetails($details = null)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details.
     *
     * @return string|null
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set violation.
     *
     * @param string|null $violation
     *
     * @return Folder
     */
    public function setViolation($violation = null)
    {
        $this->violation = $violation;

        return $this;
    }

    /**
     * Get violation.
     *
     * @return string|null
     */
    public function getViolation()
    {
        return $this->violation;
    }

    /**
     * Set isReReaded.
     *
     * @param bool $isReReaded
     *
     * @return Folder
     */
    public function setIsReReaded($isReReaded)
    {
        $this->isReReaded = $isReReaded;

        return $this;
    }

    /**
     * Get isReReaded.
     *
     * @return bool
     */
    public function getIsReReaded()
    {
        return $this->isReReaded;
    }

    /**
     * Set minute.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute
     *
     * @return Folder
     */
    public function setMinute(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute)
    {
        $this->minute = $minute;

        return $this;
    }

    /**
     * Get minute.
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Get control.
     *
     * @return Control
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * Add natinf.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Natinf $natinf
     *
     * @return Folder
     */
    public function addNatinf(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Natinf $natinf)
    {
        $this->natinfs[] = $natinf;

        return $this;
    }

    /**
     * Remove natinf.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Natinf $natinf
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeNatinf(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Natinf $natinf)
    {
        return $this->natinfs->removeElement($natinf);
    }

    /**
     * Get natinfs.
     *
     * @return Collection
     */
    public function getNatinfs()
    {
        return $this->natinfs;
    }

    /**
     * Add tagsNature.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tagsNature
     *
     * @return Folder
     */
    public function addTagsNature(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tagsNature)
    {
        $this->tagsNature[] = $tagsNature;

        return $this;
    }

    /**
     * Remove tagsNature.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tagsNature
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTagsNature(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tagsNature)
    {
        return $this->tagsNature->removeElement($tagsNature);
    }

    /**
     * Get tagsNature.
     *
     * @return Collection
     */
    public function getTagsNature()
    {
        return $this->tagsNature;
    }

    /**
     * Add tagsTown.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tagsTown
     *
     * @return Folder
     */
    public function addTagsTown(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tagsTown)
    {
        $this->tagsTown[] = $tagsTown;

        return $this;
    }

    /**
     * Remove tagsTown.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tagsTown
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTagsTown(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tagsTown)
    {
        return $this->tagsTown->removeElement($tagsTown);
    }

    /**
     * Get tagsTown.
     *
     * @return Collection
     */
    public function getTagsTown()
    {
        return $this->tagsTown;
    }

    /**
     * Add humansByMinute.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByMinute
     *
     * @return Folder
     */
    public function addHumansByMinute(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByMinute)
    {
        $this->humansByMinute[] = $humansByMinute;

        return $this;
    }

    /**
     * Remove humansByMinute.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByMinute
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeHumansByMinute(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByMinute)
    {
        return $this->humansByMinute->removeElement($humansByMinute);
    }

    /**
     * Get humansByMinute.
     *
     * @return Collection
     */
    public function getHumansByMinute()
    {
        return $this->humansByMinute;
    }

    /**
     * Add humansByFolder.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByFolder
     *
     * @return Folder
     */
    public function addHumansByFolder(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByFolder)
    {
        $this->humansByFolder[] = $humansByFolder;

        return $this;
    }

    /**
     * Remove humansByFolder.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByFolder
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeHumansByFolder(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByFolder)
    {
        return $this->humansByFolder->removeElement($humansByFolder);
    }

    /**
     * Get humansByFolder.
     *
     * @return Collection
     */
    public function getHumansByFolder()
    {
        return $this->humansByFolder;
    }

    /**
     * Set courier.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier|null $courier
     *
     * @return Folder
     */
    public function setCourier(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier $courier = null)
    {
        $this->courier = $courier;

        return $this;
    }

    /**
     * Get courier.
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier|null
     */
    public function getCourier()
    {
        return $this->courier;
    }

    /**
     * Set edition.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\FolderEdition|null $edition
     *
     * @return Folder
     */
    public function setEdition(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\FolderEdition $edition = null)
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition.
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\FolderEdition|null
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * Remove element.
     *
     * @param ElementChecked $element
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeElement(ElementChecked $element)
    {
        return $this->elements->removeElement($element);
    }

    /**
     * Get elements.
     *
     * @return Collection
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Get folderSigned.
     *
     * @return Media|null
     */
    public function getFolderSigned()
    {
        return $this->folderSigned;
    }

    /**
     * Add annex.
     *
     * @param Media $annex
     *
     * @return Folder
     */
    public function addAnnex(Media $annex)
    {
        $this->annexes[] = $annex;

        return $this;
    }

    /**
     * Remove annex.
     *
     * @param Media $annex
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAnnex(Media $annex)
    {
        return $this->annexes->removeElement($annex);
    }

    /**
     * Get annexes.
     *
     * @return Collection
     */
    public function getAnnexes()
    {
        return $this->annexes;
    }
}
