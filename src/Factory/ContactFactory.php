<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Contact;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Contact>
 */
class ContactFactory extends PersistentObjectFactory
{
    public const DEFAULT_UUID = '01964943-6945-70b2-9209-bbc3f877aa72';

    public static function class(): string
    {
        return Contact::class;
    }

    public function noRandom(): static
    {
        return $this->with([
            'name' => 'some name',
            'address' => AddressFactory::new()->noRandom(),
            'category' => CategoryFactory::new()->noRandom(),

            'uuid' => Uuid::fromString(static::DEFAULT_UUID),
            'createdAt' => new \DateTime('2015-11-01 00:00:00', new \DateTimeZone('Europe/Paris')),
            'updatedAt' => new \DateTime('2015-11-01 02:00:00', new \DateTimeZone('Europe/Paris')),
        ]);
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->word(),
            'address' => AddressFactory::new(),
            'category' => CategoryFactory::new(),

            'uuid' => Uuid::fromString(self::faker()->uuid()),
            'createdAt' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
        ];
    }
}
