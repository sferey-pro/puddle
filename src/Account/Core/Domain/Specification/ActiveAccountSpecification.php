<?php

namespace Account\Core\Domain\Specification;

use Kernel\Domain\Specification\CompositeSpecification;
use Account\Core\Domain\Account;

final class ActiveAccountSpecification extends CompositeSpecification
{
    public function failureReason(): ?string {
        return 'Account is not active';
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        return $candidate instanceof Account
            && $candidate->isActive()
            && !$candidate->isSuspended()
            && $candidate->isVerified();
    }
}
