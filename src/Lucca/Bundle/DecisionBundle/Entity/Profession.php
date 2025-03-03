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
use Lucca\Bundle\DecisionBundle\Repository\ProfessionRepository;
use Lucca\Bundle\LogBundle\Entity\LogInterface;

#[ORM\Entity(repositoryClass: ProfessionRepository::class)]
#[ORM\Table(name: "lucca_minute_profession")]
class Profession implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $name;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $company;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountActivity = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function getLogName(): string
    {
        return 'Profession';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setCompany(string $company): static
    {
        $this->company = $company;
        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setAmountActivity(?int $amountActivity): static
    {
        $this->amountActivity = $amountActivity;
        return $this;
        }
        public function getAmountActivity(): ?int
    {
        return $this->amountActivity;
    }
}
