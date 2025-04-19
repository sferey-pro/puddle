<?php

declare(strict_types=1);

namespace DataFixtures;

use App\Config\Role;
use App\Config\SocialNetwork;
use App\Factory\UserFactory;
use App\Factory\UserLoginFactory;
use App\Factory\UserSocialNetworkFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadCustomers();
    }

    protected function loadCustomers(): void
    {
        $this->loadUserBySocialNetwork(2, SocialNetwork::GOOGLE);
        $this->loadUserBySocialNetwork(2, SocialNetwork::GITHUB);

        $this->loadUserLoginsByEmails(2);

        // Create 10 User random with role user
        UserFactory::createMany(5, ['roles' => [Role::USER]]);
    }

    protected function loadUserBySocialNetwork(int $length, SocialNetwork $socialNetwork)
    {
        $users = UserFactory::createMany($length);

        UserSocialNetworkFactory::new(['socialNetwork' => $socialNetwork])
            ->distribute('user', $users)
            ->create();
    }

    protected function loadUserLoginsByEmails(int $length)
    {
        $users = UserFactory::createMany($length);

        UserLoginFactory::new()
            ->distribute('user', $users)
            ->create();
    }
}
