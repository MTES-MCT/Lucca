<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\FolderBundle\Repository\CourierRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Entity(repositoryClass: CourierRepository::class)]
#[ORM\Table(name: 'lucca_minute_courier')]
class Courier implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Folder::class, cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Folder $folder;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    #[ORM\Column(nullable: true)]
    private ?DateTime $dateOffender = null;

    #[ORM\Column(nullable: true)]
    private ?DateTime $dateJudicial = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $context = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private ?bool $civilParty = false;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $amount = null;

    #[ORM\Column(nullable: true)]
    private ?DateTime $dateDdtm = null;

    #[ORM\OneToOne(targetEntity: CourierEdition::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    private ?CourierEdition $edition = null;

    #[ORM\OneToMany(targetEntity: CourierHumanEdition::class, mappedBy: 'courier', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $humansEditions;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->humansEditions = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Courrier';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFolder(): Folder
    {
        return $this->folder;
    }

    public function setFolder(Folder $folder): self
    {
        $this->folder = $folder;
        $folder->setCourier($this);

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getDateOffender(): ?DateTime
    {
        return $this->dateOffender;
    }

    public function setDateOffender(?DateTime $dateOffender): self
    {
        $this->dateOffender = $dateOffender;

        return $this;
    }

    public function getDateJudicial(): ?DateTime
    {
        return $this->dateJudicial;
    }

    public function setDateJudicial(?DateTime $dateJudicial): self
    {
        $this->dateJudicial = $dateJudicial;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getCivilParty(): ?bool
    {
        return $this->civilParty;
    }

    public function setCivilParty(?bool $civilParty): self
    {
        $this->civilParty = $civilParty;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDateDdtm(): ?DateTime
    {
        return $this->dateDdtm;
    }

    public function setDateDdtm(?DateTime $dateDdtm): self
    {
        $this->dateDdtm = $dateDdtm;

        return $this;
    }

    public function getEdition(): ?CourierEdition
    {
        return $this->edition;
    }

    public function setEdition(?CourierEdition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    public function getHumansEditions(): Collection
    {
        return $this->humansEditions;
    }

    public function addHumansEdition(CourierHumanEdition $humanEdition): self
    {
        if (!$this->humansEditions->contains($humanEdition)) {
            $this->humansEditions[] = $humanEdition;
            $humanEdition->setCourier($this);
        }

        return $this;
    }

    public function removeHumansEdition(CourierHumanEdition $humanEdition): void
    {
        $this->humansEditions->removeElement($humanEdition);
    }
}
