<?php

declare(strict_types=1);

namespace SharedKernel\Infrastructure\Persistence\Doctrine\Types;

use Kernel\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectStringType;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;

final class EmailAddressType extends AbstractValueObjectStringType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const string NAME = 'email_address';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return EmailAddress::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
