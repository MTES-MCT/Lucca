<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\ContradictoryRepository;
use Lucca\Bundle\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContradictoryRepository::class)]
#[ORM\Table(name: "lucca_minute_contradictory")]
class Contradictory implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: "dateNoticeDdtm", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateNoticeDdtm = null;

    #[ORM\Column(name: "dateExecution", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateExecution = null;

    #[ORM\Column(name: "dateAnswer", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateAnswer = null;

    #[ORM\Column(name: "answer", type: Types::TEXT, nullable: true)]
    private ?string $answer = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Procédure contradictoire';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set dateNoticeDdtm
     *
     * @param \DateTime $dateNoticeDdtm
     *
     * @return Contradictory
     */
    public function setDateNoticeDdtm($dateNoticeDdtm): static
    {
        $this->dateNoticeDdtm = $dateNoticeDdtm;

        return $this;
    }

    /**
     * Get dateNoticeDdtm
     *
     * @return \DateTime
     */
    public function getDateNoticeDdtm(): ?\DateTime
    {
        return $this->dateNoticeDdtm;
    }

    /**
     * Set dateExecution
     *
     * @param \DateTime $dateExecution
     *
     * @return Contradictory
     */
    public function setDateExecution(\DateTime $dateExecution): static
    {
        $this->dateExecution = $dateExecution;

        return $this;
    }

    /**
     * Get dateExecution
     *
     * @return \DateTime
     */
    public function getDateExecution(): ?\DateTime
    {
        return $this->dateExecution;
    }

    /**
     * Set dateAnswer
     *
     * @param \DateTime $dateAnswer
     *
     * @return Contradictory
     */
    public function setDateAnswer(\DateTime $dateAnswer): static
    {
        $this->dateAnswer = $dateAnswer;

        return $this;
    }

    /**
     * Get dateAnswer
     *
     * @return \DateTime
     */
    public function getDateAnswer(): ?\DateTime
    {
        return $this->dateAnswer;
    }

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return Contradictory
     */
    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer(): ?string
    {
        return $this->answer;
    }
}
