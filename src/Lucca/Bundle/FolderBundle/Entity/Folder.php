<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LogInterface;
use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\MediaBundle\Entity\MediaAsyncInterface;
use Lucca\Bundle\MediaBundle\Entity\MediaListAsyncInterface;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\Human;
use Lucca\Bundle\MinuteBundle\Entity\Minute;

/**
 * Folder
 *
 * @package Lucca\Bundle\FolderBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
#[ORM\Table(name: "lucca_minute_folder")]
#[ORM\Entity(repositoryClass: "Lucca\Bundle\FolderBundle\Repository\FolderRepository")]
class Folder implements LogInterface, MediaAsyncInterface, MediaListAsyncInterface
{
    use TimestampableTrait;

    const TYPE_FOLDER = 'choice.type.folder';
    const TYPE_REFRESH = 'choice.type.refresh';
    const NATURE_HUT = 'choice.nature.hut';
    const NATURE_OTHER = 'choice.nature.other';
    const NATURE_OBSTACLE = 'choice.nature.obstacle';
    const NATURE_FORMAL_OFFENSE = 'choice.nature.formalOffense';
    const NATURE_SUBSTANTIVE_OFFENSE = 'choice.nature.substantiveOffense';
    const REASON_OBS_REFUSE_ACCESS_AFTER_LETTER = 'choice.reason_obs.refuseAccessAfterLetter';
    const REASON_OBS_REFUSE_BY_RECIPIENT = 'choice.reason_obs.refuseByRecipient';
    const REASON_OBS_UNCLAIMED_BY_RECIPIENT = 'choice.reason_obs.unclaimedByRecipient';
    const REASON_OBS_ACCESS_REFUSED = 'choice.reason_obs.accessRefused';
    const REASON_OBS_ABSENT_DURING_CONTROL = 'choice.reason_obs.absentDuringControl';

    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id;

    #[ORM\Column(name: "num", type: "string", length: 25, unique: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    private string $num;

    #[ORM\ManyToOne(targetEntity: Minute::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Minute $minute;

    #[ORM\OneToOne(targetEntity: Control::class, mappedBy: "folder")]
    #[ORM\JoinColumn(nullable: false)]
    private Control $control;

    #[ORM\ManyToMany(targetEntity: Natinf::class)]
    #[ORM\JoinTable(name: "lucca_minute_folder_linked_natinf",
        joinColumns: [new ORM\JoinColumn(name: "folder_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "natinf_id", referencedColumnName: "id")]
    )]
    private Collection $natinfs;

    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: "lucca_minute_folder_linked_tag_nature",
        joinColumns: [new ORM\JoinColumn(name: "folder_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "tag_id", referencedColumnName: "id")]
    )]
    private Collection $tagsNature;

    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: "lucca_minute_folder_linked_tag_town",
        joinColumns: [new ORM\JoinColumn(name: "folder_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "tag_id", referencedColumnName: "id")]
    )]
    private Collection $tagsTown;

    #[ORM\ManyToMany(targetEntity: Human::class)]
    #[ORM\JoinTable(name: "lucca_minute_folder_linked_human_minute",
        joinColumns: [new ORM\JoinColumn(name: "folder_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "human_id", referencedColumnName: "id")]
    )]
    private Collection $humansByMinute;

    #[ORM\ManyToMany(targetEntity: Human::class, cascade: ["persist"])]
    #[ORM\JoinTable(name: "lucca_minute_folder_linked_human_folder",
        joinColumns: [new ORM\JoinColumn(name: "folder_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "human_id", referencedColumnName: "id")]
    )]
    private Collection $humansByFolder;

    #[ORM\OneToOne(targetEntity: Courier::class, inversedBy: "folder", orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?Courier $courier = null;

    #[ORM\Column(name: "type", type: "string", length: 30, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    private ?string $type = null;

    #[ORM\Column(name: "nature", type: "string", length: 100, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    private ?string $nature = null;

    #[ORM\Column(name: "reasonObstacle", type: "string", length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    private ?string $reasonObstacle = null;

    #[ORM\Column(name: "dateClosure", type: "datetime", nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateClosure = null;

    #[ORM\Column(name: "ascertainment", type: "text", nullable: true)]
    private ?string $ascertainment = null;

    #[ORM\Column(name: "details", type: "text", nullable: true)]
    private ?string $details = null;

    #[ORM\Column(name: "violation", type: "text", nullable: true)]
    private ?string $violation = null;

    #[ORM\OneToOne(targetEntity: "Lucca\FolderBundle\Entity\FolderEdition", cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(nullable: true)]
    private ?FolderEdition $edition = null;

    #[ORM\OneToMany(targetEntity: "Lucca\FolderBundle\Entity\ElementChecked", mappedBy: "folder", cascade: ["persist", "remove"], orphanRemoval: true)]
    #[ORM\OrderBy(["position" => "ASC"])]
    private Collection $elements;

    #[ORM\Column(name: "isReReaded", type: "boolean")]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private bool $isReReaded = false;

    #[ORM\ManyToOne(targetEntity: "Lucca\MediaBundle\Entity\Media", cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Media $folderSigned = null;

    #[ORM\ManyToMany(targetEntity: "Lucca\MediaBundle\Entity\Media", cascade: ["persist", "remove"])]
    #[ORM\JoinTable(name: "lucca_folder_linked_media",
        joinColumns: [new ORM\JoinColumn(name: "page_id", referencedColumnName: "id", onDelete: "cascade")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "media_id", referencedColumnName: "id")]
    )]
    private Collection $annexes;

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
        $this->annexes = new ArrayCollection();
    }

    /**
     * Add element
     * Override
     *
     * @param ElementChecked $element
     * @return Folder
     */
    public function addElement(ElementChecked $element): static
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
    public function setControl(Control $control): static
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
    public function hasTag($string): bool
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
    public function hasNatinf($string): bool
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
    public function setFolderSigned(Media $folderSigned = null): static
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
    public function setAsyncMedia(Media $media = null): MediaAsyncInterface
    {
        $this->setFolderSigned($media);

        return $this;
    }

    /**
     * Get media by asynchronous method.
     *
     * @return Media|null
     */
    public function getAsyncMedia(): ?Media
    {
        return $this->getFolderSigned();
    }

    /**
     * Add media by asynchronous method.
     *
     * @param Media $media
     * @param string|null $vars
     * @return MediaListAsyncInterface
     */
    public function addAsyncMedia(Media $media, string $vars = null): MediaListAsyncInterface
    {
        $this->addAnnex($media);

        return $this;
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
    public function removeAsyncMedia(Media $media, string $vars = null): bool
    {
        return $this->removeAnnex($media);
    }

    /**
     * Get medias by asynchronous method.
     *
     * @return Collection
     */
    public function getAsyncMedias(): Collection
    {
        return $this->getAnnexes();
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Procès verbal';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId(): ?int
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
    public function setNum(string $num): static
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num.
     *
     * @return string
     */
    public function getNum(): string
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
    public function setType(?string $type = null): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string|null
     */
    public function getType(): ?string
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
    public function setNature(?string $nature = null): static
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get nature.
     *
     * @return string|null
     */
    public function getNature(): ?string
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
    public function setReasonObstacle(?string $reasonObstacle = null): static
    {
        $this->reasonObstacle = $reasonObstacle;

        return $this;
    }

    /**
     * Get reasonObstacle.
     *
     * @return string|null
     */
    public function getReasonObstacle(): ?string
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
    public function setDateClosure(\DateTime $dateClosure = null): static
    {
        $this->dateClosure = $dateClosure;

        return $this;
    }

    /**
     * Get dateClosure.
     *
     * @return \DateTime|null
     */
    public function getDateClosure(): ?\DateTime
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
    public function setAscertainment(?string $ascertainment = null): static
    {
        $this->ascertainment = $ascertainment;

        return $this;
    }

    /**
     * Get ascertainment.
     *
     * @return string|null
     */
    public function getAscertainment(): ?string
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
    public function setDetails(?string $details = null): static
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details.
     *
     * @return string|null
     */
    public function getDetails(): ?string
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
    public function setViolation(?string $violation = null): static
    {
        $this->violation = $violation;

        return $this;
    }

    /**
     * Get violation.
     *
     * @return string|null
     */
    public function getViolation(): ?string
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
    public function setIsReReaded(bool $isReReaded): static
    {
        $this->isReReaded = $isReReaded;

        return $this;
    }

    /**
     * Get isReReaded.
     *
     * @return bool
     */
    public function getIsReReaded(): bool
    {
        return $this->isReReaded;
    }

    /**
     * Set minute.
     *
     * @param Minute $minute
     *
     * @return Folder
     */
    public function setMinute(Minute $minute): static
    {
        $this->minute = $minute;

        return $this;
    }

    /**
     * Get minute.
     *
     * @return Minute
     */
    public function getMinute(): Minute
    {
        return $this->minute;
    }

    /**
     * Get control.
     *
     * @return Control
     */
    public function getControl(): Control
    {
        return $this->control;
    }

    /**
     * Add natinf.
     *
     * @param Natinf $natinf
     *
     * @return Folder
     */
    public function addNatinf(Natinf $natinf): static
    {
        $this->natinfs[] = $natinf;

        return $this;
    }

    /**
     * Remove natinf.
     *
     * @param Natinf $natinf
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeNatinf(Natinf $natinf): bool
    {
        return $this->natinfs->removeElement($natinf);
    }

    /**
     * Get natinfs.
     *
     * @return Collection
     */
    public function getNatinfs(): ArrayCollection|Collection
    {
        return $this->natinfs;
    }

    /**
     * Add tagsNature.
     *
     * @param Tag $tagsNature
     *
     * @return Folder
     */
    public function addTagsNature(Tag $tagsNature): static
    {
        $this->tagsNature[] = $tagsNature;

        return $this;
    }

    /**
     * Remove tagsNature.
     *
     * @param Tag $tagsNature
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTagsNature(Tag $tagsNature): bool
    {
        return $this->tagsNature->removeElement($tagsNature);
    }

    /**
     * Get tagsNature.
     *
     * @return Collection
     */
    public function getTagsNature(): ArrayCollection|Collection
    {
        return $this->tagsNature;
    }

    /**
     * Add tagsTown.
     *
     * @param Tag $tagsTown
     *
     * @return Folder
     */
    public function addTagsTown(Tag $tagsTown): static
    {
        $this->tagsTown[] = $tagsTown;

        return $this;
    }

    /**
     * Remove tagsTown.
     *
     * @param Tag $tagsTown
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTagsTown(Tag $tagsTown): bool
    {
        return $this->tagsTown->removeElement($tagsTown);
    }

    /**
     * Get tagsTown.
     *
     * @return Collection
     */
    public function getTagsTown(): ArrayCollection|Collection
    {
        return $this->tagsTown;
    }

    /**
     * Add humansByMinute.
     *
     * @param Human $humansByMinute
     *
     * @return Folder
     */
    public function addHumansByMinute(Human $humansByMinute): static
    {
        $this->humansByMinute[] = $humansByMinute;

        return $this;
    }

    /**
     * Remove humansByMinute.
     *
     * @param Human $humansByMinute
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeHumansByMinute(Human $humansByMinute): bool
    {
        return $this->humansByMinute->removeElement($humansByMinute);
    }

    /**
     * Get humansByMinute.
     *
     * @return Collection
     */
    public function getHumansByMinute(): ArrayCollection|Collection
    {
        return $this->humansByMinute;
    }

    /**
     * Add humansByFolder.
     *
     * @param Human $humansByFolder
     *
     * @return Folder
     */
    public function addHumansByFolder(Human $humansByFolder): static
    {
        $this->humansByFolder[] = $humansByFolder;

        return $this;
    }

    /**
     * Remove humansByFolder.
     *
     * @param Human $humansByFolder
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeHumansByFolder(Human $humansByFolder): bool
    {
        return $this->humansByFolder->removeElement($humansByFolder);
    }

    /**
     * Get humansByFolder.
     *
     * @return Collection
     */
    public function getHumansByFolder(): ArrayCollection|Collection
    {
        return $this->humansByFolder;
    }

    /**
     * Set courier.
     *
     * @param Courier|null $courier
     *
     * @return Folder
     */
    public function setCourier(Courier $courier = null): static
    {
        $this->courier = $courier;

        return $this;
    }

    /**
     * Get courier.
     *
     * @return Courier|null
     */
    public function getCourier(): ?Courier
    {
        return $this->courier;
    }

    /**
     * Set edition.
     *
     * @param FolderEdition|null $edition
     *
     * @return Folder
     */
    public function setEdition(FolderEdition $edition = null): static
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition.
     *
     * @return FolderEdition|null
     */
    public function getEdition(): ?FolderEdition
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
    public function removeElement(ElementChecked $element): bool
    {
        return $this->elements->removeElement($element);
    }

    /**
     * Get elements.
     *
     * @return Collection
     */
    public function getElements(): ArrayCollection|Collection
    {
        return $this->elements;
    }

    /**
     * Get folderSigned.
     *
     * @return Media|null
     */
    public function getFolderSigned(): ?Media
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
    public function addAnnex(Media $annex): static
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
    public function removeAnnex(Media $annex): bool
    {
        return $this->annexes->removeElement($annex);
    }

    /**
     * Get annexes.
     *
     * @return ArrayCollection|Collection
     */
    public function getAnnexes(): ArrayCollection|Collection
    {
        return $this->annexes;
    }
}
