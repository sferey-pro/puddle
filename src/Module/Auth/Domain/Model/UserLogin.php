<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Model;

use App\Module\Auth\Domain\Repository\UserLoginRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\UserLoginId;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: UserLoginRepositoryInterface::class)]
#[ORM\Table(name: '`user_logins`')]
class UserLogin
{
    use TimestampableEntity;

    public function __construct(
        #[ORM\Embedded(columnPrefix: false)]
        private readonly UserLoginId $identifier,
        #[ORM\Column]
        private ?\DateTimeImmutable $expiresAt = null,
        #[ORM\ManyToOne(inversedBy: 'userLogins')]
        #[ORM\JoinColumn(nullable: false)]
        private ?UserAccount $user = null,
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

    public function expiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function user(): ?UserAccount
    {
        return $this->user;
    }

    public function hash(): ?string
    {
        return $this->hash;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function verified(): static
    {
        $this->isVerified = true;

        return $this;
    }

    public function ipAddress(): ?string
    {
        return $this->ipAddress;
    }
}
