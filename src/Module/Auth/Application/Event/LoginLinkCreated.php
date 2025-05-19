<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Event;

use App\Module\Auth\Domain\User;
use App\Module\Auth\Domain\ValueObject\UserLoginId;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;

final class LoginLinkCreated
{
    public function __construct(
        public User $user,
        public UserLoginId $identifier,
        public LoginLinkDetails $loginLinkDetails,
        public string $ipAddressClient,
    ) {
    }
}
