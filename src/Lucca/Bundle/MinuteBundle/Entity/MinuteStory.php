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

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MinuteBundle\Repository\MinuteStoryRepository;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Table(name: 'lucca_minute_minute_story')]
#[ORM\Entity(repositoryClass: MinuteStoryRepository::class)]
class MinuteStory implements LoggableInterface
{
    use TimestampableTrait;

    /** STATUS constants */
    const STATUS_OPEN       = 'choice.statusMinute.open';
    const STATUS_CONTROL    = 'choice.statusMinute.control';
    const STATUS_FOLDER     = 'choice.statusMinute.folder';
    const STATUS_COURIER    = 'choice.statusMinute.courier';
    const STATUS_AIT        = 'choice.statusMinute.ait';
    const STATUS_UPDATING   = 'choice.statusMinute.updating';
    const STATUS_DECISION   = 'choice.statusMinute.decision';
    const STATUS_CLOSURE    = 'choice.statusMinute.closure';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Minute::class, inversedBy: 'historic')]
    #[ORM\JoinColumn(nullable: false)]
    private Minute $minute;

    #[ORM\Column]
    #[Assert\DateTime(message: 'constraint.datetime')]
    private \DateTime $dateUpdate;

    #[ORM\Column(length: 50)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice(choices: [
        Minute::STATUS_OPEN,
        Minute::STATUS_CONTROL,
        Minute::STATUS_FOLDER,
        Minute::STATUS_COURIER,
        Minute::STATUS_AIT,
        Minute::STATUS_UPDATING,
        Minute::STATUS_DECISION,
        Minute::STATUS_CLOSURE,
    ], message: 'constraint.closure.initiatingStructure')]
    private string $status;

    #[ORM\ManyToOne(targetEntity: Adherent::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Adherent $updatingBy;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Department $department;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * @ihneritdoc
     */
    public function getLogName(): string
    {
        return 'Historique du dossier';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDateUpdate(\DateTime $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    public function getDateUpdate(): \DateTime
    {
        return $this->dateUpdate;
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

    public function setMinute(Minute $minute): self
    {
        $this->minute = $minute;

        return $this;
    }

    public function getMinute(): Minute
    {
        return $this->minute;
    }

    public function setUpdatingBy(Adherent $updatingBy): self
    {
        $this->updatingBy = $updatingBy;

        return $this;
    }

    public function getUpdatingBy(): Adherent
    {
        return $this->updatingBy;
    }

    public function setDepartment(Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }
}
