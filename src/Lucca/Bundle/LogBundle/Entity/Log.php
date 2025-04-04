<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\LogBundle\Entity;

use DateTime, DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\LogBundle\Repository\LogRepository;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Table(name: 'lucca_log')]
class Log
{
    /** Constants */
    const STATUS_INSERT = "status.insert";
    const STATUS_UPDATE = "status.update";
    const STATUS_REMOVE = "status.remove";
    const STATUS_CONNECTION = "status.connection";

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user;

    #[ORM\Column(nullable: true)]
    private ?int $objectId = null;

    #[ORM\Column]
    private string $classname;

    #[ORM\Column(length: 20)]
    private string $status;

    #[ORM\Column]
    protected DateTimeImmutable $createdAt;

    #[ORM\Column]
    private string $shortMessage;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        /** Store immediate date */
        $this->setCreatedAt(new DateTimeImmutable('now'));
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getObjectId(): ?int
    {
        return $this->objectId;
    }

    public function setObjectId(int $objectId): self
    {
        $this->objectId = $objectId;

        return $this;
    }

    public function getClassname(): string
    {
        return $this->classname;
    }

    public function setClassname(string $classname): self
    {
        $this->classname = $classname;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getShortMessage(): string
    {
        return $this->shortMessage;
    }

    public function setShortMessage(string $shortMessage): self
    {
        $this->shortMessage = $shortMessage;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
