<?php

declare(strict_types=1);

namespace Tests\Story;

use App\Module\Auth\Domain\Enum\Role;
use Symfony\Component\Uid\Uuid;
use Tests\Factory\UserAccountFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'user')]
final class UserStory extends Story
{
    public function build(): void
    {
        UserAccountFactory::createOne([
            'email' => 'john.wick@gmail.com',
            'id'  => Uuid::fromString('01964423-39d5-774d-ae34-0fd9d6d0b1ba'),
            'roles' => [Role::SUPER_ADMIN],
        ]);

        UserAccountFactory::createOne([
            'email' => 'bryan.mills@gmail.com',
            'id'  => Uuid::fromString('01964424-5c18-792a-925a-d92a3aa95e7e'),
            'roles' => [Role::ADMIN],
        ]);
    }

    /** @todo à regarder : https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#withstory-attribute */
}
