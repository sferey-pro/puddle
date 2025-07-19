<?php

declare(strict_types=1);

namespace Account\Lifecycle\Domain\Specification;

use Account\Core\Domain\Model\Account;
use Kernel\Domain\Specification\CompositeSpecification;

final class CanBeDeletedSpecification extends CompositeSpecification
{
    public function isSatisfiedBy(mixed $candidate): bool
    {
        return $candidate instanceof Account
            && $candidate->canBeDeleted();
    }

    public function failureReason(): ?string
    {
        return 'Account cannot be deleted in its current state';
    }
}
