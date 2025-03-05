<?php

declare(strict_types=1);

namespace App\Messenger\Event;

use Symfony\Component\Uid\Uuid;

final class UserRegistered
{
    public function __construct(
        private Uuid $uuid,
    ) {
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }
}
