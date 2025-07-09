<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Types;

use App\Core\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectStringType;
use App\Module\Auth\Domain\ValueObject\IpAddress;

final class IpAddressType extends AbstractValueObjectStringType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const NAME = 'ip_address';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return IpAddress::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
