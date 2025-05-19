<?php

declare(strict_types=1);

namespace App\Core\Specification;

abstract class AbstractSpecification
{
    abstract public function isSatisfiedBy(mixed $candidate): bool;
}
