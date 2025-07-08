<?php

declare(strict_types=1);

namespace App\Core\Domain\ValueObject;

use App\Core\Domain\Validation\ValidationResult;

final class ValueObjectResult
{
    private function __construct(
        private readonly ?object $valueObject,
        private readonly ValidationResult $validationResult
    ) {}

    public static function success(object $valueObject): self
    {
        return new self($valueObject, ValidationResult::create());
    }

    public static function failure(ValidationResult $validationResult): self
    {
        return new self(null, $validationResult);
    }

    public function isSuccess(): bool
    {
        return !$this->validationResult->hasErrors();
    }

    public function isFailure(): bool
    {
        return $this->validationResult->hasErrors();
    }

    public function getValueObject(): object
    {
        return $this->valueObject ?? throw new \LogicException('No valid value object');
    }

    public function getValidationResult(): ValidationResult
    {
        return $this->validationResult;
    }
}
