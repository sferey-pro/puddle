<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Core\Domain\ValueObject\AbstractStringValueObject;
use App\Core\Domain\ValueObject\UniqueValueInterface;
use Assert\Assert;

final readonly class PhoneNumber extends AbstractStringValueObject implements UniqueValueInterface
{

    /**
     * @return Result<self> Un Result contenant un PhoneNumber en cas de succès.
     */
    public static function create(string $phone): Result
    {
        try {
            $normalizedPhone = self::normalize($phone);
            Assert::that($normalizedPhone)
                ->notEmpty('Phone number cannot be empty.')
                ->maxLength(15, 'Phone number cannot exceed 15 characters.')
                ->regex('/^\+?[0-9\s\-()]+$/', 'Phone number "%s" is not valid. It should contain only digits, spaces, dashes, parentheses, and an optional leading plus sign.');
            return Result::success(new self($normalizedPhone));
        } catch (\InvalidArgumentException $e) {
            return Result::failure(new \DomainException($e->getMessage()));
        }
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
