<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\AdditionalCost;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<AdditionalCost>
 */
final class AdditionalCostFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '01964d4f-ccea-7f09-a14c-5c48bf991616';

    public static function class(): string
    {
        return AdditionalCost::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'name' => 'some name',
            'price' => 42.0,
            'type' => 'some type',

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
            'price' => self::faker()->randomFloat(nbMaxDecimals: 2, min: 1, max: 4),
            'type' => self::faker()->word(),

            'uuid' => Uuid::fromString(static::DEFAULT_UUID),
            'createdAt' => new \DateTime('2015-11-01 00:00:00', new \DateTimeZone('Europe/Paris')),
            'updatedAt' => new \DateTime('2015-11-01 02:00:00', new \DateTimeZone('Europe/Paris')),
            'createdBy' => UserFactory::new(),
            'updatedBy' => UserFactory::new(),
        ];
    }
}
