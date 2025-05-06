<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\RawMaterialList;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<RawMaterialList>
 */
final class RawMaterialListFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '0196502d-d0d9-766d-ae35-548995a83a6f';

    public static function class(): string
    {
        return RawMaterialList::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'product' => ProductFactory::new()->random(),
            'rawMaterialItems' => [
                RawMaterialItemFactory::new()->random()
            ],
            'quantity' => 4,
            'unit' => 'some unit',

            'uuid' => Uuid::fromString(static::DEFAULT_UUID),
            'createdAt' => new \DateTime('2015-11-01 00:00:00', new \DateTimeZone('Europe/Paris')),
            'updatedAt' => new \DateTime('2015-11-01 02:00:00', new \DateTimeZone('Europe/Paris')),
        ]);
    }

    protected function defaults(): array|callable
    {
        return [
            'uuid' => Uuid::fromString(self::faker()->uuid()),
            'createdAt' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
        ];
    }
}
