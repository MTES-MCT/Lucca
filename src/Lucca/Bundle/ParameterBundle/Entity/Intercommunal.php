<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\{ToggleableTrait, TimestampableTrait};
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\ParameterBundle\Repository\IntercommunalRepository;

#[ORM\Entity(repositoryClass: IntercommunalRepository::class)]
#[ORM\Table(name: 'lucca_parameter_intercommunal')]
class Intercommunal implements LoggableInterface
{
    use ToggleableTrait, TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 1, max: 20, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $code;

    #[ORM\Column(length: 70)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 70, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Town::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Town $office = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Get label display on form
     */
    public function getFormLabel(): string
    {
        return '( ' . $this->getCode() . ' ) ' . $this->getName();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Intercommunalité';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getOffice(): ?Town
    {
        return $this->office;
    }

    public function setOffice(?Town $office): self
    {
        $this->office = $office;
        return $this;
    }
}
