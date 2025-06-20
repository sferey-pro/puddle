<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\ReadModel;

class UserView
{
    public string $id;
    public string $status;
    public ?string $email = null;
    public ?string $username = null;
    public ?string $avatarUrl = null;
    public ?bool $isVerified = null;

    public ?string $displayName = null;
    public ?string $firstName = null;
    public ?string $lastName = null;

    public ?\DateTimeImmutable $registeredAt = null;
    public ?\DateTimeImmutable $updatedAt = null;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    // Setters pour les champs actifs
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

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

    public function setAvatarUrl(?string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function setRegisteredAt(?\DateTimeImmutable $registeredAt): self
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
