<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use App\Core\Domain\ValueObject\UniqueValueInterface;
use Webmozart\Assert\Assert;

final class Email implements \Stringable, UniqueValueInterface
{
    public readonly string $value;

    public function __construct(string $value)
    {
        Assert::lengthBetween($value, 1, 180);

        $this->ensureIsValidEmail($value);

        $this->value = $value;
    }

    public static function uniqueFieldPath(): string
    {
        return 'email.value';
    }

    public function uniqueValue(): string
    {
        return $this->value;
    }

    public static function fromString(string $email)
    {
        return new self($email);
    }

    public function isEqualTo(self $email): bool
    {
        return $email->value === $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    protected function ensureIsValidEmail(string $email): void
    {
        if (false === filter_var($email, \FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(\sprintf('The email <%s> is not valid', $email));
        }
    }
}
