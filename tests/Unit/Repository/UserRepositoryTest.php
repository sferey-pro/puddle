<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Factory\UserFactory;
use function Zenstruck\Foundry\Persistence\repository;

#[CoversClass(UserRepository::class)]
#[UsesClass(User::class)]
#[UsesClass(UserFactory::class)]
#[UsesClass(\App\Factory\UserFactory::class)]
class UserRepositoryTest extends RepositoryTestCase
{
    #[Test]
    public function findByEmail(): void
    {
        // 1. Arrange
        UserFactory::createOne([
            'email' => 'jack.reacher@example.com', // This test only requires the email field - all other fields are random data
        ]);

        // 2. Act
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'jack.reacher@example.com'])
        ;

        // 3. Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('jack.reacher@example.com', $user->getEmail());
    }

    #[Test]
    public function findAll(): void
    {
        // 0. Pre-Assert by Story
        $this->assertCount(2, repository(User::class));

        // 1. Arrange
        UserFactory::createMany(10);

        // 2. Act
        $users = $this->entityManager
            ->getRepository(User::class)
            ->findAll();

        // 3. Assert
        $this->assertCount(12, $users);
    }
}
