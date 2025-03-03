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
use Lucca\Bundle\FolderBundle\Repository\MayorLetterRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LogInterface;

/**
 * Plot
 *
 * @package Lucca\Bundle\FolderBundle\Entity
 * @author Lisa <lisa.alvarez@numeric-wave.eu>
 */
#[ORM\Table(name: "lucca_minute_mayor_letter")]
#[ORM\Entity(repositoryClass: MayorLetterRepository::class)]
class MayorLetter implements LogInterface
{
    use TimestampableTrait;

    const GENDER_MALE = 'choice.gender.male';
    const GENDER_FEMALE = 'choice.gender.female';

    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id;

    #[ORM\Column(name: "gender", type: "string", length: 30)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 30, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $gender;

    #[ORM\Column(name: "name", type: "string", length: 150)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 150, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $name;

    #[ORM\Column(name: "address", type: "string", length: 255, nullable: false)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 255, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $address;

    #[ORM\Column(name: "dateSended", type: "datetime", nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateSended = null;

    #[ORM\ManyToOne(targetEntity: Town::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Town $town;

    #[ORM\ManyToOne(targetEntity: Adherent::class, inversedBy: "agents")]
    #[ORM\JoinColumn(nullable: false)]
    private Adherent $adherent;

    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Agent $agent = null;

    #[ORM\ManyToMany(targetEntity: Folder::class, cascade: ["persist"])]
    #[ORM\JoinTable(name: "lucca_mayor_letter_linked_folder",
        joinColumns: [new ORM\JoinColumn(name: "mayor_letter_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "folder_id", referencedColumnName: "id", onDelete: "CASCADE")]
    )]
    private $folders;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * Same as "Lettre au maire"
     * @return string
     */
    public function getLogName(): string
    {
        return 'Courrier de rattachement';
    }

    /**
     * ! Set folders overwrite all folders previously add
     *
     * @param array $folders
     */
    public function setFolders(array $folders): void
    {
        $this->folders = new ArrayCollection($folders);
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->folders = new ArrayCollection();
    }

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
     * Set gender.
     *
     * @param string $gender
     *
     * @return MayorLetter
     */
    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender.
     *
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return MayorLetter
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return MayorLetter
     */
    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Set dateSended.
     *
     * @param \DateTime $dateSended
     *
     * @return MayorLetter
     */
    public function setDateSended(\DateTime $dateSended): static
    {
        $this->dateSended = $dateSended;

        return $this;
    }

    /**
     * Get dateSended.
     *
     * @return \DateTime
     */
    public function getDateSended(): ?\DateTime
    {
        return $this->dateSended;
    }

    /**
     * Set town.
     *
     * @param Town $town
     *
     * @return MayorLetter
     */
    public function setTown(Town $town): static
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town.
     *
     * @return Town
     */
    public function getTown(): Town
    {
        return $this->town;
    }

    /**
     * Set adherent.
     *
     * @param Adherent $adherent
     *
     * @return MayorLetter
     */
    public function setAdherent(Adherent $adherent): static
    {
        $this->adherent = $adherent;

        return $this;
    }

    /**
     * Get adherent.
     *
     * @return Adherent
     */
    public function getAdherent(): Adherent
    {
        return $this->adherent;
    }

    /**
     * Set agent.
     *
     * @param Agent|null $agent
     *
     * @return MayorLetter
     */
    public function setAgent(Agent $agent = null): static
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent.
     *
     * @return Agent|null
     */
    public function getAgent(): Agent|null
    {
        return $this->agent;
    }

    /**
     * Add folder.
     *
     * @param Folder $folder
     *
     * @return MayorLetter
     */
    public function addFolder(Folder $folder): static
    {
        $this->folders[] = $folder;

        return $this;
    }

    /**
     * Remove folder.
     *
     * @param Folder $folder
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFolder(Folder $folder): bool
    {
        return $this->folders->removeElement($folder);
    }

    /**
     * Get folders.
     *
     * @return ArrayCollection|Collection
     */
    public function getFolders(): ArrayCollection|Collection
    {
        return $this->folders;
    }
}
