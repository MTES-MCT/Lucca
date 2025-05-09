<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MinuteBundle\Repository\ClosureRepository;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'lucca_minute_closure')]
#[ORM\Entity(repositoryClass: ClosureRepository::class)]
class Closure implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    /** TYPE constants status */
    const STATUS_REGULARIZED = 'choice.status.regularized';
    const STATUS_EXEC_OFFICE = 'choice.status.exec_office';
    const STATUS_RELAXED     = 'choice.status.relaxed';
    const STATUS_OTHER       = 'choice.status.other';

    /** TYPE constants nature */
    const NATURE_REGULARIZED_ADMINISTRATIVE = 'choice.natureRegularized.administrative';
    const NATURE_REGULARIZED_FIELD          = 'choice.natureRegularized.field';

    /** TYPE constants initiatingStructure */
    const INITSTRUCT_DDTM  = 'choice.initstruct.ddtm';
    const INITSTRUCT_DDT  = 'choice.initstruct.ddt';
    const INITSTRUCT_TOWN  = 'choice.initstruct.town';
    const INITSTRUCT_OTHER = 'choice.initstruct.other';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Minute::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Minute $minute;

    #[ORM\Column(length: 35)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    private string $status;

    #[ORM\Column]
    private \DateTime $dateClosing;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice(choices: [
        self::NATURE_REGULARIZED_ADMINISTRATIVE,
        self::NATURE_REGULARIZED_FIELD,
    ], message: 'constraint.closure.natureRegularized')]
    private ?string $natureRegularized = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice(choices: [
        self::INITSTRUCT_DDTM,
        self::INITSTRUCT_DDT,
        self::INITSTRUCT_TOWN,
        self::INITSTRUCT_OTHER,
    ], message: 'constraint.closure.initiatingStructure')]
    private ?string $initiatingStructure = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $reason = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function getLogName(): string
    {
        return 'Cloture';
    }

    /********************************************************************* Manual Getters & Setters *********************************************************************/

    public function setMinute(Minute $minute): self
    {
        $this->minute = $minute;
        $minute->setClosure($this);

        return $this;
    }

    public function setMinuteOpen(Minute $minute): self
    {
        $this->minute = $minute;
        $minute->setClosure(null);
        $minute->setIsClosed(false);

        return $this;
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setDateClosing(\DateTime $dateClosing): self
    {
        $this->dateClosing = $dateClosing;

        return $this;
    }

    public function getDateClosing(): \DateTime
    {
        return $this->dateClosing;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setNatureRegularized(?string $natureRegularized): self
    {
        $this->natureRegularized = $natureRegularized;

        return $this;
    }

    public function getNatureRegularized(): ?string
    {
        return $this->natureRegularized;
    }

    public function setInitiatingStructure(?string $initiatingStructure): self
    {
        $this->initiatingStructure = $initiatingStructure;

        return $this;
    }

    public function getInitiatingStructure(): ?string
    {
        return $this->initiatingStructure;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function getMinute(): Minute
    {
        return $this->minute;
    }
}
