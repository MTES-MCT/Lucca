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
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\LiquidationRepository;
use Lucca\Bundle\LogBundle\Entity\LogInterface;

#[ORM\Entity(repositoryClass: LiquidationRepository::class)]
#[ORM\Table(name: "lucca_minute_liquidation")]
class Liquidation implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateStart = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateEnd = null;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountPenalty = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Liquidation';
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
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return Liquidation
     */
    public function setDateStart(\DateTime $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart(): ?\DateTime
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return Liquidation
     */
    public function setDateEnd(\DateTime $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd(): ?\DateTime
    {
        return $this->dateEnd;
    }

    /**
     * Set amountPenalty
     *
     * @param int $amountPenalty
     *
     * @return Liquidation
     */
    public function setAmountPenalty(int $amountPenalty): static
    {
        $this->amountPenalty = $amountPenalty;

        return $this;
    }

    /**
     * Get amountPenalty
     *
     * @return int
     */
    public function getAmountPenalty(): ?int
    {
        return $this->amountPenalty;
    }
}
