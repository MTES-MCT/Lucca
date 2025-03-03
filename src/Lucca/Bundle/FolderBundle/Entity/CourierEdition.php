<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CourierEdition
 *
 * @package Lucca\Bundle\FolderBundle\Entity
 */
#[ORM\Table(name: 'lucca_minute_courier_edition')]
#[ORM\Entity(repositoryClass: 'Lucca\Bundle\FolderBundle\Repository\CourierEditionRepository')]
class CourierEdition implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id;

    #[ORM\Column(name: 'judicialEdited', type: 'boolean')]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private bool $judicialEdited = false;

    #[ORM\Column(name: 'letterJudicial', type: 'text', nullable: true)]
    private ?string $letterJudicial = null;

    #[ORM\Column(name: 'ddtmEdited', type: 'boolean')]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private bool $ddtmEdited = false;

    #[ORM\Column(name: 'letterDdtm', type: 'text', nullable: true)]
    private ?string $letterDdtm = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Courrier édition';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): int
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