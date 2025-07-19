<?php

declare(strict_types=1);

namespace Account\Lifecycle\Domain\Specification;

use Account\Core\Domain\Model\Account;
use Kernel\Domain\Specification\CompositeSpecification;

final class CanBeReactivatedSpecification extends CompositeSpecification
{
    public function isSatisfiedBy(mixed $candidate): bool
    {
        return $candidate instanceof Account
            && $candidate->canBeReactivated();
    }

    public function failureReason(): ?string
    {
        return 'Account cannot be reactivated in its current state';
    }
}
