<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\CoreBundle\Entity\ToggleableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\ParameterBundle\Entity\Tribunal;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[ORM\Table(name: 'lucca_adherent_agent')]
class Agent implements LoggableInterface
{
    /** Traits */
    use ToggleableTrait, TimestampableTrait;

    /** FUNCTION constants */
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

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $firstname;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $function;

    #[ORM\ManyToOne(targetEntity: Tribunal::class, cascade: ['persist'])]
    private ?Tribunal $tribunal = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 25, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $commission = null;

    #[ORM\ManyToOne(targetEntity: Adherent::class, inversedBy: 'agents')]
    #[ORM\JoinColumn(nullable: false)]
    private Adherent $adherent;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Get full name with official syntax
     */
    public function getOfficialName(): string
    {
        return $this->getName() . ' ' . $this->getFirstname();
    }

    /**
     * Get label display on form
     */
    public function getFormLabel(): string
    {
        return '(' . $this->getCommission() . ') ' . $this->getOfficialName();
    }

    /**
     * Log name of this Class
     */
    public function getLogName(): string
    {
        return 'Agent';
    }

    /**
     * Constraint on date sent
     * If data is set, check others date
     */
    #[Assert\Callback]
    public function commissionConstraint(ExecutionContextInterface $context): void
    {
        /** Date send must be greater or equal than date Postal */
        if (in_array($this->getFunction(), array(self::FUNCTION_MAYOR, self::FUNCTION_DEPUTY)) && $this->getCommission() !== null) {
            $context->buildViolation('constraint.agent.commission_must_be_null')
                ->atPath('function')
                ->addViolation();

        } else if (!in_array($this->getFunction(), array(self::FUNCTION_MAYOR, self::FUNCTION_DEPUTY)) && $this->getCommission() === null) {
            $context->buildViolation('constraint.agent.commission_is_required')
                ->atPath('function')
                ->addViolation();
        }
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFunction(): string
    {
        return $this->function;
    }

    public function setFunction(string $function): self
    {
        $this->function = $function;

        return $this;
    }

    public function getTribunal(): ?Tribunal
    {
        return $this->tribunal;
    }

    public function setTribunal(?Tribunal $tribunal): self
    {
        $this->tribunal = $tribunal;

        return $this;
    }

    public function getCommission(): ?string
    {
        return $this->commission;
    }

    public function setCommission(?string $commission): self
    {
        $this->commission = $commission;

        return $this;
    }

    public function getAdherent(): Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(Adherent $adherent): self
    {
        $this->adherent = $adherent;

        return $this;
    }
}
