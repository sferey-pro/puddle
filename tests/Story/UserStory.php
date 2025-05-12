<?php

declare(strict_types=1);

namespace Tests\Story;

use App\Config\Role;
use App\Entity\ValueObject\UserId;
use Symfony\Component\Uid\Uuid;
use Tests\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserStory extends Story
{
    public function build(): void
    {
        UserFactory::createOne([
            'email' => 'john.wick@gmail.com',
            'identifier'  => new UserId(Uuid::fromString('01964423-39d5-774d-ae34-0fd9d6d0b1ba')),
            'roles' => [Role::SUPER_ADMIN],
        ]);

        UserFactory::createOne([
            'email' => 'bryan.mills@gmail.com',
            'identifier'  => new UserId(Uuid::fromString('01964424-5c18-792a-925a-d92a3aa95e7e')),
            'roles' => [Role::ADMIN],
        ]);
    }
}
