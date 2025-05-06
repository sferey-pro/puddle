<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\RawMaterialItem;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<RawMaterialItem>
 */
final class RawMaterialItemFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '0196502b-7aab-73e2-9180-5f78740fca92';

    public static function class(): string
    {
        return RawMaterialItem::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'rawMaterial' => RawMaterialFactory::new()->random(),
            'quantity' => 4,
            'unit' => 'some unit',

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
            'quantity' => self::faker()->randomDigit(),
            'unit' => self::faker()->word(),

            'uuid' => Uuid::fromString(self::faker()->uuid()),
            'createdAt' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
            'createdBy' => UserFactory::new(),
            'updatedBy' => UserFactory::new(),
        ];
    }
}
