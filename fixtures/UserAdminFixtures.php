<?php

declare(strict_types=1);

namespace DataFixtures;

use App\Config\Role;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserAdminFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers();
    }

    protected function loadUsers(): void
    {
        // Create Admin user
        UserFactory::createOne([
            'email' => 'john.wick@gmail.com',
            'roles' => [Role::SUPER_ADMIN],
        ]);

        UserFactory::createOne([
            'email' => 'bryan.mills@gmail.com',
            'roles' => [Role::ADMIN],
        ]);
    }
}
