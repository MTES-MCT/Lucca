<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\UserBundle\Repository\GroupRepository;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'constraint.unique.group.name', errorPath: 'name')]
#[ORM\Table(name: 'lucca_user_group')]
class Group
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Length(min: 2, max: 180, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(type: Types::TEXT, options: ['comment' => '(DC2Type:array)'])]
    private string $roles = '';

    #[ORM\Column]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private bool $displayed = true;

    /************************************************************************ Custom functions ************************************************************************/

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function addRole(string $role): self
    {
        $roles = unserialize($this->roles);
        $roles[] = $role;

        $this->roles = serialize(array_unique($roles));

        return $this;
    }

    public function removeRole(string $role): self
    {
        $roles = unserialize($this->roles);
        if (false !== $key = array_search(strtoupper($role), $roles, true)) {
            unset($roles[$key]);
        }

        $this->roles = serialize(array_unique($roles));

        return $this;
    }

    public function getRoles(): array
    {
        return unserialize($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = serialize($roles);

        return $this;
    }

    /**
     * Log name of this Class
     */
    public function getLogName(): string
    {
        return 'Groupe';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
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

    public function isDisplayed(): bool
    {
        return $this->displayed;
    }

    public function setDisplayed(bool $displayed): self
    {
        $this->displayed = $displayed;

        return $this;
    }
}
