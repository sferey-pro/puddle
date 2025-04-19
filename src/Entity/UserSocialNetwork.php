<?php

declare(strict_types=1);

namespace App\Entity;

use App\Config\SocialNetwork;
use App\Repository\UserSocialNetworkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSocialNetworkRepository::class)]
#[ORM\Table(name: '`user_social_networks`')]
class UserSocialNetwork extends AbstractEntity
{
    #[ORM\ManyToOne(inversedBy: 'userSocialNetworks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(enumType: SocialNetwork::class)]
    private ?SocialNetwork $socialNetwork = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private $socialId;

    #[ORM\Column]
    private ?bool $isActive = null;

    public function jsonSerialize(): array
    {
        return [];
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

    public function getSocialNetwork(): ?SocialNetwork
    {
        return $this->socialNetwork;
    }

    public function setSocialNetwork(SocialNetwork $socialNetwork): static
    {
        $this->socialNetwork = $socialNetwork;

        return $this;
    }

    public function getSocialId()
    {
        return $this->socialId;
    }

    public function setSocialId($socialId): static
    {
        $this->socialId = $socialId;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function setAsActive(): static
    {
        $this->isActive = true;

        return $this;
    }

    public function setAsInactive(): static
    {
        $this->isActive = false;

        return $this;
    }
}
