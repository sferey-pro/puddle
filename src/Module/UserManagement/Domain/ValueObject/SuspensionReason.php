<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Value Object représentant la raison de la suspension d'un compte.
 * Il garantit qu'une raison n'est jamais vide.
 */
final readonly class SuspensionReason
{
    public string $value;

    private function __construct(string $reason)
    {
        Assert::notEmpty($reason, 'Suspension reason cannot be empty.');
        Assert::maxLength($reason, 255);

        $this->value = $reason;
    }

    public static function create(string $reason): self
    {
        return new self($reason);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
