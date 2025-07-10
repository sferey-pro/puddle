<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Infrastructure\Doctrine\Types;

use App\Core\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectStringType;
use App\Module\SharedContext\Domain\ValueObject\PhoneNumber;

final class PhoneNumberType extends AbstractValueObjectStringType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const NAME = 'phone_number';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return PhoneNumber::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
