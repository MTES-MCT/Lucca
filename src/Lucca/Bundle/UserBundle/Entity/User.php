<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\{PasswordAuthenticatedUserInterface, UserInterface};
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\UserBundle\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'constraint.unique.email', errorPath: 'email')]
#[ORM\Table(name: 'lucca_user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /** Constant ROLE */
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $name = null;

    #[ORM\JoinTable(name: 'lucca_user_linked_group')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'group_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Group::class)]
    private Collection $groups;

    #[ORM\Column(length: 180)]
    #[Assert\Length(max: 180, maxMessage: 'constraint.length.max')]
    private string $username;

    #[ORM\Column(length: 180)]
    #[Assert\Length(max: 180, maxMessage: 'constraint.length.max')]
    private string $usernameCanonical;

    #[ORM\Column(length: 180)]
    #[Assert\Length(max: 180, maxMessage: 'constraint.length.max')]
    private string $email;

    #[ORM\Column(length: 180)]
    #[Assert\Length(max: 180, maxMessage: 'constraint.length.max')]
    private string $emailCanonical;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private bool $enabled = true;

    /**
     * The salt to use for hashing.
     */
    #[ORM\Column(nullable: true)]
    private ?string $salt = null;

    /**
     * Encrypted password. Must be persisted.
     */
    #[ORM\Column]
    private string $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     */
    #[ORM\Column]
    private string $plainPassword;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $lastLogin = null;

    /**
     * Random string sent to the user email address in order to verify it.
     */
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $confirmationToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $passwordRequestedAt = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->groups = new ArrayCollection();

        $this->setEnabled(true);
    }

    /**
     *  @inheritdoc
     */
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function addRole(string $role): User
    {
        $this->roles[] = $role;

        return $this;
    }

    public function removeRole(string $role): User
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        /** Add each role contained in Group entities on this list */
        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * Get label displayed on forms
     */
    public function getFormLabel(): string
    {
        return '(' . $this->getId() . ') ' . $this->getName() . ' - ' . $this->getEmail();
    }

    /**
     * Log name of this Class
     */
    public function getLogName(): string
    {
        return 'Utilisateur';
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

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        $this->groups->removeElement($group);

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsernameCanonical(): string
    {
        return $this->usernameCanonical;
    }

    public function setUsernameCanonical(string $usernameCanonical): self
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailCanonical(): string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(string $emailCanonical): self
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setIsEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?DateTime $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getPasswordRequestedAt(): ?DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?DateTime $passwordRequestedAt): self
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
