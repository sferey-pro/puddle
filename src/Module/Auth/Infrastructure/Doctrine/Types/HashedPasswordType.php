<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Types;

use App\Core\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectStringType;
use App\Module\Auth\Domain\ValueObject\HashedPassword;

final class HashedPasswordType extends AbstractValueObjectStringType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const NAME = 'hashed_password';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return HashedPassword::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
