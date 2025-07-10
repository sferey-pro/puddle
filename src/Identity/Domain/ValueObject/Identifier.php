<?php

declare(strict_types=1);

namespace Identity\Domain\ValueObject;

use Kernel\Domain\ValueObject\UniqueValueInterface;
use Kernel\Domain\ValueObject\ValueObjectInterface;

/**
 * Interface marqueur pour représenter l'identité principale d'un utilisateur.
 */
interface Identifier extends UniqueValueInterface
{
    public function value(): string;

    public function equals(Identifier $other): bool;

    public function getClass(): string;
}
