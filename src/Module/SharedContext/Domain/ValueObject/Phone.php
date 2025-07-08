<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use App\Core\Domain\Validation\ValidationResult;
use App\Core\Domain\ValueObject\UniqueValueInterface;
use App\Module\SharedContext\Domain\Exception\InvalidPhoneException;
use Webmozart\Assert\Assert;

final class Phone implements \Stringable, UniqueValueInterface
{
    public readonly string $value;

    public function __construct(string $value)
    {
        try {
            Assert::notEmpty($value);
            Assert::regex($value, '/^\+?[1-9]\d{1,14}$/', 'Invalid phone number format.');
        } catch (\InvalidArgumentException $e) {
            throw InvalidPhoneException::fromValue($value, $e);
        }

        $this->value = $value;
    }

    public static function uniqueFieldPath(): string
    {
        return 'phone.value';
    }

    public function uniqueValue(): string
    {
        return $this->value;
    }

    public static function fromString(string $phone)
    {
        return new self($phone);
    }

    public function isEqualTo(self $phone): bool
    {
        return $phone->value === $this->value;
    }

    public static function validate(string $value): ValidationResult
    {
        $result = ValidationResult::create();

        try {
            new self($value);
        } catch (InvalidPhoneException $e) {
            $result->addError($e->getMessage());
        }

        return $result;
    }

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
