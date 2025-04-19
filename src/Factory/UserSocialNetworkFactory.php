<?php

declare(strict_types=1);

namespace App\Factory;

use App\Config\SocialNetwork;
use App\Entity\UserSocialNetwork;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<UserSocialNetwork>
 */
final class UserSocialNetworkFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '01964956-1202-72f4-b0ee-b8bb91957f03';

    public static function class(): string
    {
        return UserSocialNetwork::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'socialId' => '1234567890',
            'socialNetwork' => SocialNetwork::GOOGLE,
            'user' => UserFactory::new()->noRandom(),
            'isActive' => true,

            'uuid' => Uuid::fromString(static::DEFAULT_UUID),
            'createdAt' => new \DateTime('2015-11-01 00:00:00', new \DateTimeZone('Europe/Paris')),
            'updatedAt' => new \DateTime('2015-11-01 02:00:00', new \DateTimeZone('Europe/Paris')),
        ]);
    }

    protected function defaults(): array|callable
    {
        return [
            'socialId' => self::faker()->randomNumber(8, true),
            'socialNetwork' => self::faker()->randomElement(SocialNetwork::cases()),
            'user' => UserFactory::new(),
            'isActive' => self::faker()->boolean(),

            'uuid' => Uuid::fromString(self::faker()->uuid()),
            'createdAt' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
        ];
    }
}
