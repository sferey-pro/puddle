<?php

declare(strict_types=1);

namespace App\Messenger\Event;

use App\Entity\User;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;
use Symfony\Component\Uid\Uuid;

final class LoginLinkCreated
{
    public function __construct(
        private User $user,
        private Uuid $identifier,
        private LoginLinkDetails $loginLinkDetails,
        private string $ipAddressClient,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getIdentifier(): Uuid
    {
        return $this->identifier;
    }

    public function getLoginLinkDetails(): LoginLinkDetails
    {
        return $this->loginLinkDetails;
    }

    public function getIpAddressClient(): string
    {
        return $this->ipAddressClient;
    }
}
