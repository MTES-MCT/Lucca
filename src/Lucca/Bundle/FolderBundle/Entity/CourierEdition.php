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
use Lucca\Bundle\FolderBundle\Repository\CourierEditionRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Entity(repositoryClass: CourierEditionRepository::class)]
#[ORM\Table(name: 'lucca_minute_courier_edition')]
class CourierEdition implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private bool $judicialEdited = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $letterJudicial = null;

    #[ORM\Column]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private bool $ddtmEdited = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $letterDdtm = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Courrier édition';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setJudicialEdited(bool $judicialEdited): self
    {
        $this->judicialEdited = $judicialEdited;

        return $this;
    }

    public function getJudicialEdited(): bool
    {
        return $this->judicialEdited;
    }

    public function setLetterJudicial(?string $letterJudicial): self
    {
        $this->letterJudicial = $letterJudicial;

        return $this;
    }

    public function getLetterJudicial(): ?string
    {
        return $this->letterJudicial;
    }

    public function setDdtmEdited(bool $ddtmEdited): self
    {
        $this->ddtmEdited = $ddtmEdited;

        return $this;
    }

    public function getDdtmEdited(): bool
    {
        return $this->ddtmEdited;
    }

    public function setLetterDdtm(?string $letterDdtm): self
    {
        $this->letterDdtm = $letterDdtm;

        return $this;
    }

    public function getLetterDdtm(): ?string
    {
        return $this->letterDdtm;
    }
}
