<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\ExpulsionRepository;
use Lucca\Bundle\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExpulsionRepository::class)]
#[ORM\Table(name: "lucca_minute_expulsion")]
class Expulsion implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id;

    #[ORM\OneToOne(targetEntity: "Lucca\DecisionBundle\Entity\Decision", inversedBy: "expulsion")]
    #[ORM\JoinColumn(nullable: false)]
    private $decision;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $lawFirm;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountDelivrery;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateHearing;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateAdjournment;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateDeliberation;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateJudicialDesision;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $statusDecision;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $comment;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Expulsion';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set lawFirm
     *
     * @param string $lawFirm
     *
     * @return Expulsion
     */
    public function setLawFirm(string $lawFirm): static
    {
        $this->lawFirm = $lawFirm;

        return $this;
    }

    /**
     * Get lawFirm
     *
     * @return string
     */
    public function getLawFirm(): ?string
    {
        return $this->lawFirm;
    }

    /**
     * Set amountDelivrery
     *
     * @param integer $amountDelivrery
     *
     * @return Expulsion
     */
    public function setAmountDelivrery(int $amountDelivrery): static
    {
        $this->amountDelivrery = $amountDelivrery;

        return $this;
    }

    /**
     * Get amountDelivrery
     *
     * @return integer
     */
    public function getAmountDelivrery(): ?int
    {
        return $this->amountDelivrery;
    }

    /**
     * Set dateHearing
     *
     * @param \DateTime $dateHearing
     *
     * @return Expulsion
     */
    public function setDateHearing(\DateTime $dateHearing): static
    {
        $this->dateHearing = $dateHearing;

        return $this;
    }

    /**
     * Get dateHearing
     *
     * @return \DateTime
     */
    public function getDateHearing(): ?\DateTime
    {
        return $this->dateHearing;
    }

    /**
     * Set dateAdjournment
     *
     * @param \DateTime $dateAdjournment
     *
     * @return Expulsion
     */
    public function setDateAdjournment(\DateTime $dateAdjournment): static
    {
        $this->dateAdjournment = $dateAdjournment;

        return $this;
    }

    /**
     * Get dateAdjournment
     *
     * @return \DateTime
     */
    public function getDateAdjournment(): ?\DateTime
    {
        return $this->dateAdjournment;
    }

    /**
     * Set dateDeliberation
     *
     * @param \DateTime $dateDeliberation
     *
     * @return Expulsion
     */
    public function setDateDeliberation(\DateTime $dateDeliberation): static
    {
        $this->dateDeliberation = $dateDeliberation;

        return $this;
    }

    /**
     * Get dateDeliberation
     *
     * @return \DateTime
     */
    public function getDateDeliberation(): ?\DateTime
    {
        return $this->dateDeliberation;
    }

    /**
     * Set dateJudicialDesision
     *
     * @param \DateTime $dateJudicialDesision
     *
     * @return Expulsion
     */
    public function setDateJudicialDesision(\DateTime $dateJudicialDesision): static
    {
        $this->dateJudicialDesision = $dateJudicialDesision;

        return $this;
    }

    /**
     * Get dateJudicialDesision
     *
     * @return \DateTime
     */
    public function getDateJudicialDesision(): ?\DateTime
    {
        return $this->dateJudicialDesision;
    }

    /**
     * Set statusDecision
     *
     * @param string $statusDecision
     *
     * @return Expulsion
     */
    public function setStatusDecision(string $statusDecision): static
    {
        $this->statusDecision = $statusDecision;

        return $this;
    }

    /**
     * Get statusDecision
     *
     * @return string
     */
    public function getStatusDecision(): ?string
    {
        return $this->statusDecision;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Expulsion
     */
    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set decision
     *
     * @param Decision $decision
     *
     * @return Expulsion
     */
    public function setDecision(Decision $decision): static
    {
        $this->decision = $decision;

        return $this;
    }

    /**
     * Get decision
     *
     * @return Decision
     */
    public function getDecision(): Decision
    {
        return $this->decision;
    }
}
