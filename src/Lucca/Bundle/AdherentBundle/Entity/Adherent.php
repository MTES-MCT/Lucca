<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MediaBundle\Entity\{MediaAsyncInterface, Media};
use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\AdherentBundle\Repository\AdherentRepository;
use Lucca\Bundle\ParameterBundle\Entity\{Town, Intercommunal, Service};
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Entity(repositoryClass: AdherentRepository::class)]
#[ORM\Table(name: 'lucca_adherent')]
class Adherent implements LoggableInterface, MediaAsyncInterface
{
    use ToggleableTrait, TimestampableTrait;

    /** TYPE constants */
    const FUNCTION_MAYOR = 'choice.function.mayor';
    const FUNCTION_DEPUTY = 'choice.function.deputy';
    const FUNCTION_POLICE = 'choice.function.police';
    const FUNCTION_DGS = 'choice.function.dgs';
    const FUNCTION_DST = 'choice.function.dst';
    const FUNCTION_TOWN_MANAGER = 'choice.function.town_manager';
    const FUNCTION_ADMIN_AGENT = 'choice.function.admin_agent';
    const FUNCTION_ASVP = 'choice.function.asvp';
    const FUNCTION_COUNTRY_GUARD = 'choice.function.country_guard';
    const FUNCTION_COUNTRY_AGENT = 'choice.function.country_agent';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $firstname = '';

    #[ORM\Column(length: 50)]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $function;

    #[ORM\JoinTable(name: 'lucca_adherent_linked_department')]
    #[ORM\JoinColumn(name: 'adherent_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'department_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Department::class)]
    private Collection $departments;

    #[ORM\Column(length: 60, nullable: true)]
    #[Assert\Length(min: 2, max: 60, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $address = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Length(min: 1, max: 10, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $zipcode = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $city = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\Length(min: 5, max: 15, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $phone;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\Length(min: 5, max: 15, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $mobile = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    protected bool $invitedByMail = false;

    #[ORM\ManyToOne(targetEntity: Town::class)]
    private ?Town $town = null;

    #[ORM\ManyToOne(targetEntity: Intercommunal::class)]
    private ?Intercommunal $intercommunal = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    private ?Service $service = null;

    #[ORM\OneToMany(targetEntity: Agent::class, mappedBy: 'adherent', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $agents;

    #[ORM\Column(length: 120, nullable: true)]
    #[Assert\Length(min: 2, max: 120, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $unitAttachment = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Assert\Length(min: 2, max: 120, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $emailPublic = null;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['persist'])]
    private ?Media $logo = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->agents = new ArrayCollection();
        $this->departments = new ArrayCollection();
    }

    /**
     * Get label display on form
     */
    public function getFormLabel(): string
    {
        return '( ' . $this->getStructure()->getCode() . ' ) ' . $this->getStructure()->getName();
    }

    /**
     * Get full name with official syntax
     */
    public function getOfficialName(): string
    {
        return $this->getName() . ' ' . $this->getFirstname();
    }

    /**
     * Get full name with inline syntax
     */
    public function getOfficialAddressInline(): string
    {
        return $this->getAddress() . ', ' . $this->getZipcode() . ' ' . $this->getCity();
    }

    /**
     * Get full name with official syntax
     */
    public function getOfficialAddress(): string
    {
        $address = '';
        if ($this->getAddress()) {
            $address .= $this->getAddress() . '<br>';
        }

        if ($this->getZipCode()) {
            $address .= $this->getZipCode() . ', ';
        }

        if ($this->getCity()) {
            $address .= $this->getCity() . '<br>';
        }

        return '<address>' . $address . '</address>';
    }

    /**
     * Get official phone
     */
    public function getOfficialPhone(): string
    {
        if ($this->getPhone()) {
            return $this->getPhone();
        }

        return $this->getMobile();
    }

    /**
     * Get Structure of Adherent
     */
    public function getStructure(): Intercommunal|Service|Town
    {
        if ($this->getTown()) {
            return $this->getTown();
        }

        if ($this->getIntercommunal()) {
            return $this->getIntercommunal();
        }

        return $this->getService();
    }

    /**
     * Get Structure of Adherent
     */
    public function getStructureName(): string
    {
        if ($this->getTown()) {
            return $this->getTown()->getName();
        }

        if ($this->getIntercommunal()) {
            return $this->getIntercommunal()->getName();
        }

        return $this->getService()->getName();
    }

    /**
     * Get Structure of Adherent
     */
    public function getStructureOffice(): string
    {
        if ($this->getTown()) {
            return $this->getTown()->getOffice();
        }

        if ($this->getIntercommunal()) {
            return $this->getIntercommunal()->getOffice()->getName();
        }

        return $this->getService()->getOffice()->getName();
    }

    /**
     * Constraint on parameter
     * One of Town / Intercommunal / Service must be chosen
     */
    #[Assert\Callback]
    public function parameterConstraint(ExecutionContextInterface $context): void
    {
        if ($this->getTown() && ($this->getService() || $this->getIntercommunal())) {
            $context->buildViolation('constraint.parameter.town')
                ->atPath('name')
                ->addViolation();
        } elseif ($this->getService() && ($this->getTown() || $this->getIntercommunal())) {
            $context->buildViolation('constraint.parameter.service')
                ->atPath('name')
                ->addViolation();
        } elseif ($this->getIntercommunal() && ($this->getTown() || $this->getService())) {
            $context->buildViolation('constraint.parameter.intercommunal')
                ->atPath('name')
                ->addViolation();
        }
    }

    /**
     * Set media by asynchronous method.
     */
    public function setAsyncMedia(?Media $media = null): self
    {
        $this->setLogo($media);

        return $this;
    }

    /**
     * Get media by asynchronous method.
     */
    public function getAsyncMedia(): ?Media
    {
        return $this->getLogo();
    }

    /**
     * Log name of this Class
     */
    public function getLogName(): string
    {
        return 'Adhérent';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addGroup(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments[] = $department;
        }

        return $this;
    }

    public function removeGroup(Department $department): self
    {
        $this->departments->removeElement($department);

        return $this;
    }

    public function getFunction(): string
    {
        return $this->function;
    }

    public function setFunction(string $function): self
    {
        $this->function = $function;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getInvitedByMail(): bool
    {
        return $this->invitedByMail;
    }

    public function setInvitedByMail(bool $invitedByMail): self
    {
        $this->invitedByMail = $invitedByMail;

        return $this;
    }

    public function getTown(): ?Town
    {
        return $this->town;
    }

    public function setTown(?Town $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getIntercommunal(): ?Intercommunal
    {
        return $this->intercommunal;
    }

    public function setIntercommunal(?Intercommunal $intercommunal): self
    {
        $this->intercommunal = $intercommunal;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function addAgent(Agent $agent): self
    {
        if (!$this->agents->contains($agent)) {
            $this->agents[] = $agent;
            $agent->setAdherent($this);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): bool
    {
        return $this->agents->removeElement($agent);
    }

    public function getUnitAttachment(): ?string
    {
        return $this->unitAttachment;
    }

    public function setUnitAttachment(?string $unitAttachment): self
    {
        $this->unitAttachment = $unitAttachment;

        return $this;
    }

    public function getEmailPublic(): ?string
    {
        return $this->emailPublic;
    }

    public function setEmailPublic(?string $emailPublic): self
    {
        $this->emailPublic = $emailPublic;

        return $this;
    }

    public function setLogo(?Media $logo = null): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getLogo(): ?Media
    {
        return $this->logo;
    }
}
