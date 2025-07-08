<?php

namespace App\Core\Domain\ValueObject\Trait;

use App\Core\Domain\Validation\ValidationResult;
use App\Core\Domain\ValueObject\ValueObjectResult;
use Webmozart\Assert\Assert;

declare(strict_types=1);

trait SmartValidation
{
    protected static function validateAndCreate(
        string $value,
        array $assertions,
        callable $constructor
    ): ValueObjectResult {
        $result = ValidationResult::create();
        $lazy = Assert::lazy();

        try {
            foreach ($assertions as $assertion) {
                $lazy->that($value)->$assertion();
            }
            $lazy->verifyNow();
            return ValueObjectResult::success($constructor($value));
        } catch (LazyAssertionException $e) {
            foreach ($e->getViolations() as $violation) {
                $result->addError($violation->getMessage());
            }
            return ValueObjectResult::failure($result);
        }
    }
}
