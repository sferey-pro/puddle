<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Supplier;
use App\Entity\ValueObject\SupplierId;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Supplier>
 */
final class SupplierFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '01964952-2372-7d99-81f4-bac10fa2851a';

    public static function class(): string
    {
        return Supplier::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'name' => 'some name',

            'identifier' => new SupplierId(Uuid::fromString(static::DEFAULT_UUID)),
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

            'identifier' => new SupplierId(Uuid::fromString(self::faker()->uuid())),
            'createdAt' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
            'createdBy' => UserFactory::new(),
            'updatedBy' => UserFactory::new(),
        ];
    }
}
