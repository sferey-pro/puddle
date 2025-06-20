<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Module\Auth\Domain\Enum\SocialNetwork;

final readonly class Social
{
    public readonly ?string $socialId;
    public readonly ?SocialNetwork $socialNetwork;

    public function __construct(?string $socialId, ?SocialNetwork $socialNetwork)
    {
        $this->socialId = $socialId;
        $this->socialNetwork = $socialNetwork;
    }

    public function equals(self $other): bool
    {
        return $this->socialNetwork === $other->socialNetwork && $this->socialId === $other->socialId;
    }
}
