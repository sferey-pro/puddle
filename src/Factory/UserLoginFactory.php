<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\UserLogin;
use App\Entity\ValueObject\UserLoginId;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<UserLogin>
 */
final class UserLoginFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '01964955-ed48-71f8-ab57-a0b9624a9c92';

    public static function class(): string
    {
        return UserLogin::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'hash' => 'some hash',
            'ipAddress' => '127.0.0.1',
            'isVerified' => true,
            'user' => UserFactory::new()->noRandom(),
            'expiresAt' => new \DateTime('2015-11-01 04:00:00', new \DateTimeZone('Europe/Paris')),

            'identifier' => new UserLoginId(Uuid::fromString(static::DEFAULT_UUID)),
            'createdAt' => new \DateTime('2015-11-01 00:00:00', new \DateTimeZone('Europe/Paris')),
            'updatedAt' => new \DateTime('2015-11-01 02:00:00', new \DateTimeZone('Europe/Paris')),
        ]);
    }

    protected function defaults(): array|callable
    {
        return [
            'hash' => self::faker()->lexify('????????????'),
            'ipAddress' => self::faker()->ipv4(),
            'isVerified' => self::faker()->boolean(),
            'user' => UserFactory::new(),
            'expiresAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),

            'identifier' => new UserLoginId(Uuid::fromString(self::faker()->uuid())),
            'createdAt' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
        ];
    }
}
