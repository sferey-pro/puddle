<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Exception;

use App\Core\Domain\Exception\InvalidValueObjectException;

final class InvalidIdentifierException extends InvalidValueObjectException
{
    private const NOT_VALID_FORMAT = 'I-001';

    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function becauseItMustBeAnEmailOrPhone(): self
    {
        return new self('Invalid identifier provided. Must be a valid email or phone number.');
    }

    public function errorCode(): string
    {
        return self::NOT_VALID_FORMAT;
    }
}
