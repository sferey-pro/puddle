<?php

declare(strict_types=1);

namespace App\Messenger\Command\Security;

use App\Common\Command\CommandInterface;
use App\Entity\User;
use Symfony\Component\Uid\Uuid;

final class CreateLoginLink implements CommandInterface
{
    private Uuid $uuid;
    private User $user;

    public function __construct(Uuid $identifier, User $user)
    {
        $this->uuid = $identifier;
        $this->user = $user;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
