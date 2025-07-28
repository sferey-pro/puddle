<?php

namespace Account\Registration\Domain\Specification;

use Kernel\Domain\Specification\SpecificationInterface;

final class ValidRegistrationDataSpecification implements SpecificationInterface
{
    public function __construct() {
    }

    public function failureReason(): ?string {
        return null;
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        return true;
    }
}
