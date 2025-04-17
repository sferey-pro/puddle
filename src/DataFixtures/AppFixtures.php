<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Config\Role;
use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers();
        $this->loadProducts();
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

        // Create 10 User random with role user
        UserFactory::createMany(10, ['roles' => [Role::USER]]);
    }

    protected function loadProducts(): void
    {
        $categories = CategoryFactory::createSequence(
            [
                ['name' => 'category 1'],
                ['name' => 'category 2'],
            ]
        );

        $products = ProductFactory::new()
            ->sequence(
                [
                    ['name' => 'product 1'],
                    ['name' => 'product 2'],
                ]
            )

            // "product 1" will have "category 1" and "product 2" will have "category 2"
            ->distribute('category', $categories)
            ->create();
    }
}
