<?php

namespace App\Core\Domain\ValueObject\Trait;

use App\Core\Domain\Validation\ValidationResult;
use Webmozart\Assert\Assert;

declare(strict_types=1);

trait SelfValidating
{
    abstract protected static function getValidationRules(): array;

    public static function validate(string $value): ValidationResult
    {
        $result = ValidationResult::create();
        $lazy = Assert::lazy();

        try {
            $rules = static::getValidationRules();
            $lazyThat = $lazy->that($value);

            foreach ($rules as $rule => $params) {
                if (is_numeric($rule)) {
                    // Simple rule: ['notEmpty', 'email']
                    $lazyThat->$params();
                } else {
                    // Rule with params: ['minLength' => 5]
                    $lazyThat->$rule(...(array) $params);
                }
            }

            $lazy->verifyNow();
        } catch (LazyAssertionException $e) {
            foreach ($e->getViolations() as $violation) {
                $result->addError($violation->getMessage());
            }
        }

        return $result;
    }

    public static function tryFromString(string $value): static
    {
        $validationResult = static::validate($value);

        if (!$validationResult->isValid()) {
            throw new ValidationException($validationResult);
        }

        return static::fromString($value);
    }
}
