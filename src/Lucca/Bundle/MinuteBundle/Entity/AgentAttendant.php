<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MinuteBundle\Repository\AgentRepository;

#[ORM\Table(name: 'lucca_minute_agent_attendant')]
#[ORM\Entity(repositoryClass: AgentRepository::class)]
class AgentAttendant implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    const FUNCTION_MAYOR = 'choice.function.mayor';
    const FUNCTION_DEPUTY = 'choice.function.deputy';
    const FUNCTION_POLICE = 'choice.function.police';
    const FUNCTION_DGS = 'choice.function.dgs';
    const FUNCTION_DST = 'choice.function.dst';
    const FUNCTION_TOWN_MANAGER = 'choice.function.town_manager';
    const FUNCTION_ADMIN_AGENT = 'choice.function.admin_agent';
    const FUNCTION_COUNTRY_AGENT = 'choice.function.country_agent';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

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
        return '(' . $this->getId() . ') ' . $this->getName() . ' ' . $this->getFirstname();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Agent accompagnant';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFunction(string $function): self
    {
        $this->function = $function;

        return $this;
    }

    public function getFunction(): string
    {
        return $this->function;
    }
}
