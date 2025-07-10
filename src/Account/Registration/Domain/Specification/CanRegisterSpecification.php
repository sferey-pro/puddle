<?php

namespace Account\Registration\Domain\Specification;

use Identity\Application\Query\IsIdentityAvailable;
use Kernel\Application\Bus\QueryBusInterface;
use Kernel\Domain\Specification\CompositeSpecification;

/**
 * Specification principale qui combine toutes les règles.
 */
final class CanRegisterSpecification extends CompositeSpecification
{
    public function __construct(
        private readonly RegistrationOpenSpecification $registrationOpen,
        private readonly ValidRegistrationDataSpecification $validData,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        if (!$candidate instanceof RegistrationRequest) {
            return false;
        }

        // L'ordre est important pour l'UX
        // 1. D'abord vérifier si les inscriptions sont ouvertes
        if (!$this->registrationOpen->isSatisfiedBy($candidate)) {
            return false;
        }

        // 2. Ensuite valider les données
        if (!$this->validData->isSatisfiedBy($candidate)) {
            return false;
        }

        $isAvailable = $this->queryBus->ask(
            new IsIdentityAvailable($candidate->getIdentity())
        );

        // 3. Enfin vérifier l'unicité (plus coûteux)
        if (false === $isAvailable) {
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        // Retourne le message d'erreur approprié
        if (!$this->registrationOpen->isSatisfiedBy($this->lastCandidate)) {
            return $this->registrationOpen->getErrorMessage();
        }

        if (!$this->validData->isSatisfiedBy($this->lastCandidate)) {
            return 'Invalid registration data provided';
        }

        if (!$this->identityNotUsed->isSatisfiedBy($this->lastCandidate)) {
            return 'This identifier is already registered';
        }

        return 'Registration cannot be completed';
    }
}
