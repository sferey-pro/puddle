<?php

declare(strict_types=1);

namespace SharedKernel\Infrastructure\Persistence\Doctrine\Types;

use Kernel\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectStringType;
use SharedKernel\Domain\ValueObject\Contact\PhoneNumber;

final class PhoneNumberType extends AbstractValueObjectStringType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const string NAME = 'phone_number';

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
