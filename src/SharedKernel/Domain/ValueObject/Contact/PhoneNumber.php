<?php

declare(strict_types=1);

namespace SharedKernel\Domain\ValueObject\Contact;

use Assert\Assert;
use Kernel\Domain\ValueObject\AbstractStringValueObject;
use Kernel\Domain\ValueObject\UniqueValueInterface;
use Kernel\Domain\ValueObject\ValidatedValueObjectTrait;

final readonly class PhoneNumber extends AbstractStringValueObject implements UniqueValueInterface
{
    use ValidatedValueObjectTrait;

    private function __construct(private(set) string $value)
    {
        parent::__construct($value);
    }

    protected static function validate(string $phone): void
    {
        $normalizedPhone = self::normalize($phone);
        Assert::that($normalizedPhone)
            ->notEmpty('Phone number cannot be empty.')
            ->maxLength(15, 'Phone number cannot exceed 15 characters.')
            ->regex('/^\+?[0-9\s\-()]+$/', 'Phone number "%s" is not valid. It should contain only digits, spaces, dashes, parentheses, and an optional leading plus sign.');
    }

    private static function normalize(string $rawPhoneNumber): string
    {
        $trimmed = trim($rawPhoneNumber);
        if ($trimmed === '') {
            return '';
        }

        // Supprime tout ce qui n'est pas un chiffre
        $digitsOnly = preg_replace('/[^\d]/', '', $trimmed);

        // Si l'original commençait par '+', on s'assure que le résultat le fait aussi.
        if (str_starts_with($trimmed, '+')) {
            return '+' . $digitsOnly;
        }

        return $digitsOnly;
    }

    public static function uniqueFieldPath(): string
    {
        return 'phone';
    }

    public function uniqueValue(): string
    {
        return $this->value;
    }

}
