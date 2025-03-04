<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\AdherentBundle\Entity\{Adherent, Agent};
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\FolderBundle\Repository\MayorLetterRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\ParameterBundle\Entity\Town;

#[ORM\Table(name: "lucca_minute_mayor_letter")]
#[ORM\Entity(repositoryClass: MayorLetterRepository::class)]
class MayorLetter implements LoggableInterface
{
    use TimestampableTrait;

    const GENDER_MALE = 'choice.gender.male';
    const GENDER_FEMALE = 'choice.gender.female';

    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 30, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $gender;

    #[ORM\Column(length: 150)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 150, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $name;

    #[ORM\Column(nullable: false)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 255, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $address;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateSended = null;

    #[ORM\ManyToOne(targetEntity: Town::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Town $town;

    #[ORM\ManyToOne(targetEntity: Adherent::class, inversedBy: "agents")]
    #[ORM\JoinColumn(nullable: false)]
    private Adherent $adherent;

    #[ORM\ManyToOne(targetEntity: Agent::class)]
    private ?Agent $agent = null;

    #[ORM\ManyToMany(targetEntity: Folder::class, cascade: ["persist"])]
    #[ORM\JoinTable(name: "lucca_mayor_letter_linked_folder",
        joinColumns: [new ORM\JoinColumn(name: "mayor_letter_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "folder_id", referencedColumnName: "id", onDelete: "CASCADE")]
    )]
    private Collection $folders;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->folders = new ArrayCollection();
    }

    /**
     * ! Set folders overwrite all folders previously add
     */
    public function setFolders(array $folders): void
    {
        $this->folders = new ArrayCollection($folders);
    }

    /**
     * @ihneritdoc
     */
    public function getLogName(): string
    {
        return 'Courrier de rattachement';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
    }

    public function getDateSended(): ?DateTime
    {
        return $this->dateSended;
    }

    public function setDateSended(?DateTime $dateSended): self
    {
        $this->dateSended = $dateSended;
    }

    public function getTown(): Town
    {
        return $this->town;
    }

    public function setTown(Town $town): self
    {
        $this->town = $town;
    }

    public function getAdherent(): Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(Adherent $adherent): self
    {
        $this->adherent = $adherent;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;
    }

    public function getFolders(): Collection
    {
        return $this->folders;
    }

    public function addFolder(Folder $folder): self
    {
        if (!$this->folders->contains($folder)) {
            $this->folders->add($folder);
        }

        return $this;
    }

    public function removeFolder(Folder $folder): void
    {
        $this->folders->removeElement($folder);
    }
}
