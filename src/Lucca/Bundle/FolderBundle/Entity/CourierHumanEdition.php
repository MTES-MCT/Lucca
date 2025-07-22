<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\FolderBundle\Repository\CourierHumanEditionRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MinuteBundle\Entity\Human;

#[ORM\Table(name: "lucca_minute_courier_human_edition")]
#[ORM\Entity(repositoryClass: CourierHumanEditionRepository::class)]
class CourierHumanEdition implements LoggableInterface
{
    use TimestampableTrait;

    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Courier::class, inversedBy: "humansEditions")]
    #[ORM\JoinColumn(nullable: false)]
    private Courier $courier;

    #[ORM\ManyToOne(targetEntity: Human::class, cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private Human $human;

    #[ORM\Column]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private bool $letterOffenderEdited = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $letterOffender = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * @inheritDoc
     */
    public function getLogName(): string
    {
        return 'Courrier édition par humain';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLetterOffenderEdited(bool $letterOffenderEdited): self
    {
        $this->letterOffenderEdited = $letterOffenderEdited;

        return $this;
    }

    public function getLetterOffenderEdited(): bool
    {
        return $this->letterOffenderEdited;
    }

    public function setLetterOffender(?string $letterOffender): self
    {
        $this->letterOffender = $letterOffender;

        return $this;
    }

    public function getLetterOffender(): ?string
    {
        return $this->letterOffender;
    }

    public function setCourier(Courier $courier): self
    {
        $this->courier = $courier;

        return $this;
    }

    public function getCourier(): Courier
    {
        return $this->courier;
    }

    public function setHuman(Human $human): self
    {
        $this->human = $human;

        return $this;
    }

    public function getHuman(): Human
    {
        return $this->human;
    }
}
