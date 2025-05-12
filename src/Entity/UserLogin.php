<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserLoginRepository;
use App\Entity\ValueObject\UserLoginId;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserLoginRepository::class)]
#[ORM\Table(name: '`user_logins`')]
class UserLogin extends BaseEntity
{


    public function __construct(
        #[ORM\Embedded(columnPrefix: false)]
        private readonly UserLoginId $identifier,
        #[ORM\Column]
        private ?\DateTimeImmutable $expiresAt = null,
        #[ORM\ManyToOne(inversedBy: 'userLogins')]
        #[ORM\JoinColumn(nullable: false)]
        private ?User $user = null,
        #[ORM\Column(type: Types::TEXT)]
        private ?string $hash = null,
        #[ORM\Column]
        private ?bool $isVerified = null,
        #[ORM\Column(length: 255)]
        private ?string $ipAddress = null,

    ) {

    }

    public function identifier(): UserLoginId
    {
        return $this->identifier;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function verified(): static
    {
        $this->isVerified = true;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): static
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }
}
