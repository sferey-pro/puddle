<?php

declare(strict_types=1);

namespace App\Core\Domain\ValueObject;

use App\Core\Domain\Validation\ValidationResult;
use Webmozart\Assert\Assert;

trait WebmozartValidationTrait
{
    /**
     * Validation avec Webmozart Assert
     */
    protected static function validateWithAssert(string $value, callable $assertCallback): ValidationResult
    {
        $result = ValidationResult::create();

        try {
            $assertCallback($value);
        } catch (\InvalidArgumentException $e) {
            $result->addError($e->getMessage());
        }

        return $result;
    }

    /**
     * Validation multiple avec Webmozart Assert
     */
    protected static function validateWithMultipleAsserts(string $value, array $assertCallbacks): ValidationResult
    {
        $result = ValidationResult::create();

        foreach ($assertCallbacks as $callback) {
            try {
                $callback($value);
            } catch (\InvalidArgumentException $e) {
                $result->addError($e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Validation avec normalisation préalable
     */
    protected static function validateWithNormalizedAssert(
        string $value,
        callable $normalizer,
        callable $assertCallback
    ): ValidationResult {
        $normalizedValue = $normalizer($value);
        return self::validateWithAssert($normalizedValue, $assertCallback);
    }
}
