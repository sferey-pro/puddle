<?php

declare(strict_types=1);

namespace App\Factory;

use App\Config\Role;
use App\Entity\User;
use App\Entity\ValueObject\UserId;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '01964955-7f56-7267-8b64-b63d5aac3921';

    public function __construct(
        private ?UserPasswordHasherInterface $passwordHasher = null,
    ) {
        parent::__construct();
    }

    public static function class(): string
    {
        return User::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'email' => 'acme@localhost',
            'isVerified' => true,
            'locale' => 'fr_FR',
            'password' => '1234',
            'roles' => [Role::USER->value],

            'identifier' => new UserId(Uuid::fromString(static::DEFAULT_UUID)),
            'createdAt' => new \DateTime('2015-11-01 00:00:00', new \DateTimeZone('Europe/Paris')),
            'updatedAt' => new \DateTime('2015-11-01 02:00:00', new \DateTimeZone('Europe/Paris')),
        ]);
    }

    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'isVerified' => true,
            'locale' => self::faker()->locale(),
            'password' => '1234',
            'roles' => [Role::USER->value],

            'identifier' => new UserId(Uuid::fromString(self::faker()->uuid())),
            'createdAt' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
        ];
    }

    public function withRoleAdmin(): self
    {
        return $this->with(['roles' => [Role::ADMIN]]);
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (User $user) {
                if (null !== $this->passwordHasher) {
                    $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
                }
            })
        ;
    }
}
