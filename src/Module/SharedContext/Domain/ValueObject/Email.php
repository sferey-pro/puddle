<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use App\Core\Domain\Validation\ValidationResult;
use App\Core\Domain\ValueObject\Trait\SmartValidation;
use App\Core\Domain\ValueObject\UniqueValueInterface;
use App\Module\SharedContext\Domain\Exception\InvalidEmailException;
use Webmozart\Assert\Assert;

final readonly class Email implements \Stringable, UniqueValueInterface
{
    use SmartValidation;

    private function __construct(private string $value) {}

    public static function validate(string $value): ValidationResult
    {
        return self::validateInternal($value);
    }

    public static function fromString(string $value): self
    {
        $result = self::tryFromString($value);
        if ($result->isFailure()) {
            throw new \InvalidArgumentException('Invalid email address: '.implode(', ', $result->getErrors()->all()));
        }

        return $result->getValue();
    }

    public static function tryFromString(string $value): ValueObjectResult
    {
        return self::validateAndCreate(
            $value,
            ['lengthBetween:1,180', 'email'], // Note: 'email' pour filter_var
            fn($v) => new self($v)
        );
    }

    private static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    public function value(): string
    {
        return $this->value;
    }

    // Interface UniqueValueInterface
    public static function uniqueFieldPath(): string
    {
        return 'email.value';
    }

    public function uniqueValue(): string
    {
        return $this->value;
    }

    // Comparaison
    public function isEqualTo(self $email): bool
    {
        return $email->value === $this->value;
    }

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
