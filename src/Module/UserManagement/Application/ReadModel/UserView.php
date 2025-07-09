<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\ReadModel;

class UserView
{
    public string $id;
    public string $status;
    public ?string $email = null;
    public ?bool $isVerified = null;

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

    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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
