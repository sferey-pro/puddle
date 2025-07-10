<?php

namespace Account\Core\Domain\Specification;

use Kernel\Domain\Specification\CompositeSpecification;

/**
 * Specification qui vérifie qu'un compte est complet
 * (utilisée par Registration et Profile).
 */
final class CompleteAccountSpecification extends CompositeSpecification
{
    public function __construct(
        private readonly ActiveAccountSpecification $activeSpec,
        private readonly CompleteProfileSpecification $profileSpec
    ) {
    }

    public function failureReason(): ?string {
        return null;
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        return $this->activeSpec->isSatisfiedBy($candidate)
            && $this->profileSpec->isSatisfiedBy($candidate->getProfile());
    }
}
