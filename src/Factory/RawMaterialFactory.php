<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\RawMaterial;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<RawMaterial>
 */
final class RawMaterialFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '01964d4f-efe3-7f06-9f74-75ab371a06f2';

    public static function class(): string
    {
        return RawMaterial::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'category' => CategoryFactory::new()->noRandom(),
            'link' => 'http://localhost',
            'name' => 'some name',
            'priceVariability' => true,
            'totalCost' => 42,
            'unit' => 'some unit',
            'unitPrice' => 4.2,

            'uuid' => Uuid::fromString(static::DEFAULT_UUID),
            'createdAt' => new \DateTime('2015-11-01 00:00:00', new \DateTimeZone('Europe/Paris')),
            'updatedAt' => new \DateTime('2015-11-01 02:00:00', new \DateTimeZone('Europe/Paris')),
            'createdBy' => UserFactory::new()->noRandom(),
            'updatedBy' => UserFactory::new()->noRandom(),
        ]);
    }

    protected function defaults(): array|callable
    {
        return [
            'category' => CategoryFactory::new(),
            'link' => self::faker()->url(),
            'name' => self::faker()->word(),
            'priceVariability' => self::faker()->boolean(),
            'totalCost' => self::faker()->randomFloat(),
            'unit' => self::faker()->word(),
            'unitPrice' => self::faker()->randomFloat(),

            'uuid' => Uuid::fromString(self::faker()->uuid()),
            'createdAt' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
            'createdBy' => UserFactory::new(),
            'updatedBy' => UserFactory::new(),
        ];
    }
}
