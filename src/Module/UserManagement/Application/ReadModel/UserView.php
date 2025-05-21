<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\ReadModel;

class UserView
{
    public string $userId;
    public ?string $email = null;
    public ?string $username = null;
    public ?bool $isVerified = null;

    // public ?string $firstName = null;
    // public ?string $lastName = null;
    // public ?bool $isActive = null; // Différent de isVerified, pourrait être un statut admin
    // public ?\DateTimeImmutable $lastLogin = null;
    // public array $roles = [];
    // public ?\DateTimeImmutable $registeredAt = null;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    // Setters pour les champs actifs
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    // Setters pour les champs commentés (pour référence future)
    // public function setFirstName(?string $firstName): self { $this->firstName = $firstName; return $this; }
    // public function setLastName(?string $lastName): self { $this->lastName = $lastName; return $this; }
    // public function setIsActive(?bool $isActive): self { $this->isActive = $isActive; return $this; }
    // public function setLastLogin(?\DateTimeImmutable $lastLogin): self { $this->lastLogin = $lastLogin; return $this; }
    // public function setRoles(array $roles): self { $this->roles = $roles; return $this; }
    // public function setRegisteredAt(?\DateTimeImmutable $registeredAt): self { $this->registeredAt = $registeredAt; return $this; }
}
