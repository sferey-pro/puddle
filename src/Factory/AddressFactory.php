<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Address;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Address>
 */
final class AddressFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '01964942-d719-73ed-a14e-a65d1fae4e07';

    public static function class(): string
    {
        return Address::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'city' => 'some city',

            'uuid' => Uuid::fromString(static::DEFAULT_UUID),
            'createdAt' => new \DateTime('2015-11-01 00:00:00', new \DateTimeZone('Europe/Paris')),
            'updatedAt' => new \DateTime('2015-11-01 02:00:00', new \DateTimeZone('Europe/Paris')),
        ]);
    }

    protected function defaults(): array
    {
        return [
            'city' => self::faker()->city(),
            'uuid' => Uuid::fromString(self::faker()->uuid()),
            'createdAt' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
        ];
    }
}
