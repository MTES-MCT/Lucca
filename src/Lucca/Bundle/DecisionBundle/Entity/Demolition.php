<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\DemolitionRepository;
use Lucca\Bundle\LogBundle\Entity\LogInterface;

#[ORM\Entity(repositoryClass: DemolitionRepository::class)]
#[ORM\Table(name: "lucca_minute_demolition")]
class Demolition implements LogInterface
{
    use TimestampableTrait;

    /** RESULT constants */
    const RESULT_REALIZED = 'choice.result.realized';
    const RESULT_REPORTED = 'choice.result.reported';
    const RESULT_CANCELLED = 'choice.result.cancelled';
    const RESULT_WAITING = 'choice.result.waiting';

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id;

    #[ORM\OneToOne(targetEntity: "Lucca\DecisionBundle\Entity\Decision", inversedBy: "demolition")]
    #[ORM\JoinColumn(nullable: false)]
    private Decision $decision;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $company;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountCompany;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateDemolition;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $bailif;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountBailif;

    #[ORM\ManyToMany(targetEntity: "Lucca\DecisionBundle\Entity\Profession", cascade: ["persist", "remove"])]
    #[ORM\JoinTable(name: "lucca_minute_demolition_linked_profession")]
    #[ORM\JoinColumn(name: "demolition_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "profession_id", referencedColumnName: "id")]
    private ArrayCollection $professions;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $comment;

    #[ORM\Column(type: "string", length: 30, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 30, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $result;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Demolition constructor
     */
    public function __construct()
    {
        $this->professions = new ArrayCollection();
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Démolition';
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
     * Set company
     *
     * @param string $company
     *
     * @return Demolition
     */
    public function setCompany(string $company): static
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * Set amountCompany
     *
     * @param integer $amountCompany
     *
     * @return Demolition
     */
    public function setAmountCompany(int $amountCompany): static
    {
        $this->amountCompany = $amountCompany;

        return $this;
    }

    /**
     * Get amountCompany
     *
     * @return integer
     */
    public function getAmountCompany(): ?int
    {
        return $this->amountCompany;
    }

    /**
     * Set dateDemolition
     *
     * @param \DateTime $dateDemolition
     *
     * @return Demolition
     */
    public function setDateDemolition(\DateTime $dateDemolition): static
    {
        $this->dateDemolition = $dateDemolition;

        return $this;
    }

    /**
     * Get dateDemolition
     *
     * @return \DateTime
     */
    public function getDateDemolition(): ?\DateTime
    {
        return $this->dateDemolition;
    }

    /**
     * Set bailif
     *
     * @param string $bailif
     *
     * @return Demolition
     */
    public function setBailif(string $bailif): static
    {
        $this->bailif = $bailif;

        return $this;
    }

    /**
     * Get bailif
     *
     * @return string
     */
    public function getBailif(): ?string
    {
        return $this->bailif;
    }

    /**
     * Set amountBailif
     *
     * @param integer $amountBailif
     *
     * @return Demolition
     */
    public function setAmountBailif(int $amountBailif): static
    {
        $this->amountBailif = $amountBailif;

        return $this;
    }

    /**
     * Get amountBailif
     *
     * @return integer
     */
    public function getAmountBailif(): ?int
    {
        return $this->amountBailif;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Demolition
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
     * Set result
     *
     * @param string $result
     *
     * @return Demolition
     */
    public function setResult(string $result): static
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return string
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

    /**
     * Set decision
     *
     * @param Decision $decision
     *
     * @return Demolition
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

    /**
     * Add profession
     *
     * @param Profession $profession
     *
     * @return Demolition
     */
    public function addProfession(Profession $profession): static
    {
        $this->professions[] = $profession;

        return $this;
    }

    /**
     * Remove profession
     *
     * @param Profession $profession
     */
    public function removeProfession(Profession $profession): void
    {
        $this->professions->removeElement($profession);
    }

    /**
     * Get professions
     *
     * @return ArrayCollection|Collection
     */
    public function getProfessions(): ArrayCollection|Collection
    {
        return $this->professions;
    }
}
