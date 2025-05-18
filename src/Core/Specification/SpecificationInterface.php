<?php

declare(strict_types=1);

namespace App\Core\Specification;

interface SpecificationInterface
{
    public function isSatisfiedBy(mixed $candidate): bool;
}
