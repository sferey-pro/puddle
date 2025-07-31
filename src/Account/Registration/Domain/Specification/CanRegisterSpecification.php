<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Specification;

use Account\Registration\Domain\Model\RegistrationRequest;
use Kernel\Domain\Specification\CompositeSpecification;

/**
 * Specification principale CORRIGÉE - Plus de dépendance QueryBus
 */
final class CanRegisterSpecification extends CompositeSpecification
{
    private mixed $lastCandidate = null;

    public function __construct(
        private readonly RegistrationIsOpenSpecification $registrationOpen,
        private readonly ValidRegistrationDataSpecification $validData,
    ) {}

    public function failureReason(): ?string
    {
        if (!$this->registrationOpen->isSatisfiedBy($this->lastCandidate)) {
            return $this->registrationOpen->failureReason();
        }

        if (!$this->validData->isSatisfiedBy($this->lastCandidate)) {
            return $this->validData->failureReason();
        }

        return null;
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        if (!$candidate instanceof RegistrationRequest) {
            return false;
        }

        $this->lastCandidate = $candidate;

        return $this->registrationOpen->isSatisfiedBy($candidate)
            && $this->validData->isSatisfiedBy($candidate);
    }
}
