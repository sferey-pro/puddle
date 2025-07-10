<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Core\Domain\ValueObject\UniqueValueInterface;

/**
 * Interface marqueur pour représenter l'identité principale d'un utilisateur.
 */
interface UserIdentity extends UniqueValueInterface
{
    public function value(): string;
}
