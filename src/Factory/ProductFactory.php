<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Product>
 */
final class ProductFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '1964951-39b9-7747-8faa-25d369eb676a';

    public static function class(): string
    {
        return Product::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'name' => 'some name',
            'category' => CategoryFactory::new()->noRandom(),
            'price' => 42.0,

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
            'name' => self::faker()->word(),
            'category' => CategoryFactory::new(),
            'price' => self::faker()->randomFloat(nbMaxDecimals: 2, min: 1, max: 4),

            'uuid' => Uuid::fromString(self::faker()->uuid()),
            'createdAt' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
            'createdBy' => UserFactory::new(),
            'updatedBy' => UserFactory::new(),
        ];
    }
}
