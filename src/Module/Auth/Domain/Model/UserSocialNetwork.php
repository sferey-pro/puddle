<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Model;

use App\Module\Auth\Domain\Enum\SocialNetwork;
use App\Module\Auth\Domain\ValueObject\UserSocialNetworkId;
use App\Shared\Domain\Model\BaseModel;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`user_social_networks`')]
class UserSocialNetwork extends BaseModel
{
    #[ORM\Embedded(columnPrefix: false)]
    private readonly UserSocialNetworkId $identifier;

    public function __construct(
        #[ORM\Column(type: Types::STRING, length: 255)]
        private $socialId,
        #[ORM\ManyToOne(inversedBy: 'userSocialNetworks')]
        #[ORM\JoinColumn(nullable: false)]
        private ?User $user = null,
        #[ORM\Column(enumType: SocialNetwork::class)]
        private ?SocialNetwork $socialNetwork = null,
        #[ORM\Column]
        private ?bool $isActive = null,
    ) {
    }

    public function identifier(): UserSocialNetworkId
    {
        return $this->identifier;
    }
}
