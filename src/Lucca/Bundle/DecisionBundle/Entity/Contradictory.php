<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\ContradictoryRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Entity(repositoryClass: ContradictoryRepository::class)]
#[ORM\Table(name: "lucca_minute_contradictory")]
class Contradictory implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateNoticeDdtm = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateExecution = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateAnswer = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $answer = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Procédure contradictoire';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateNoticeDdtm(): ?DateTime
    {
        return $this->dateNoticeDdtm;
    }

    public function setDateNoticeDdtm(?DateTime $dateNoticeDdtm): self
    {
        $this->dateNoticeDdtm = $dateNoticeDdtm;

        return $this;
    }

    public function getDateExecution(): ?DateTime
    {
        return $this->dateExecution;
    }

    public function setDateExecution(?DateTime $dateExecution): self
    {
        $this->dateExecution = $dateExecution;

        return $this;
    }

    public function getDateAnswer(): ?DateTime
    {
        return $this->dateAnswer;
    }

    public function setDateAnswer(?DateTime $dateAnswer): self
    {
        $this->dateAnswer = $dateAnswer;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }
}
