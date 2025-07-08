<?php

declare(strict_types=1);

namespace App\Core\Domain\ValueObject;

final class ValidationResult
{
    /** @var Violation[] */
    private array $violations = [];

    private function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public static function withViolations(array $violations): self
    {
        $result = new self();
        $result->violations = $violations;
        return $result;
    }

    public function addError(string $message, string $code = 'VALIDATION_ERROR', ?string $property = null, mixed $invalidValue = null): void
    {
        $this->violations[] = new Violation($message, $code, $property, $invalidValue);
    }

    public function addViolation(Violation $violation): void
    {
        $this->violations[] = $violation;
    }

    public function isValid(): bool
    {
        return empty($this->violations);
    }

    public function hasErrors(): bool
    {
        return !$this->isValid();
    }

    /** @return Violation[] */
    public function violations(): array
    {
        return $this->violations;
    }

    /** @return string[] */
    public function getErrors(): array
    {
        return array_map(fn(Violation $v) => $v->message, $this->violations);
    }

    public function getErrorsAsString(string $separator = ', '): string
    {
        return implode($separator, $this->getErrors());
    }

}
