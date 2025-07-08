<?php

namespace App\Core\Domain\ValueObject;

declare(strict_types=1);

final class Violation
{
    public function __construct(
        private(set) string $message,
        private(set) string $code,
        private(set) ?string $property = null,
        private(set) mixed $invalidValue = null
    ) {}
}
