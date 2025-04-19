<?php

declare(strict_types=1);

namespace App\Entity;

use App\Config\Role;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<Role> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $locale = null;

    /**
     * @var Collection<int, UserLogin>
     */
    #[ORM\OneToMany(targetEntity: UserLogin::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userLogins;

    /**
     * @var Collection<int, UserSocialNetwork>
     */
    #[ORM\OneToMany(targetEntity: UserSocialNetwork::class, mappedBy: 'userAccount', orphanRemoval: true, cascade: ['persist'])]
    private Collection $userSocialNetworks;

    public function __construct()
    {
        $this->userLogins = new ArrayCollection();
        $this->userSocialNetworks = new ArrayCollection();
    }

    public function jsonSerialize(): array
    {
        return [
            'email' => $this->getEmail(),
        ];
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return list<Role|null>
     */
    public function getEnumRoles(): array
    {
        return array_map(fn (string $role): ?Role => Role::tryFrom($role), $this->getRoles());
    }

    public function getMainRole(): Role
    {
        return Role::USER;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return Collection<int, UserLogin>
     */
    public function getUserLogins(): Collection
    {
        return $this->userLogins;
    }

    public function addUserLogin(UserLogin $userLogin): static
    {
        if (!$this->userLogins->contains($userLogin)) {
            $this->userLogins->add($userLogin);
            $userLogin->setUser($this);
        }

        return $this;
    }

    public function removeUserLogin(UserLogin $userLogin): static
    {
        if ($this->userLogins->removeElement($userLogin)) {
            // set the owning side to null (unless already changed)
            if ($userLogin->getUser() === $this) {
                $userLogin->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserSocialNetwork>
     */
    public function getUserSocialNetworks(): Collection
    {
        return $this->userSocialNetworks;
    }

    public function addUserSocialNetwork(UserSocialNetwork $userSocialNetwork): static
    {
        if (!$this->userSocialNetworks->contains($userSocialNetwork)) {
            $this->userSocialNetworks->add($userSocialNetwork);
            $userSocialNetwork->setUser($this);
        }

        return $this;
    }

    public function removeUserSocialNetwork(UserSocialNetwork $userSocialNetwork): static
    {
        if ($this->userSocialNetworks->removeElement($userSocialNetwork)) {
            // set the owning side to null (unless already changed)
            if ($userSocialNetwork->getUser() === $this) {
                $userSocialNetwork->setUser(null);
            }
        }

        return $this;
    }
}
