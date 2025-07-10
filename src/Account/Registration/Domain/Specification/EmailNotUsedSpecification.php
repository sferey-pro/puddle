<?php

namespace Account\Registration\Domain\Specification;

use Identity\Domain\Repository\UserIdentityRepositoryInterface;
use Identity\Domain\ValueObject\EmailIdentity;
use Kernel\Domain\Specification\SpecificationInterface;

/**
 * Vérifie qu'une adresse email n'est pas déjà utilisée.
 * Interroge le contexte Identity pour la vérification.
 */
final class EmailNotUsedSpecification implements SpecificationInterface
{
    public function __construct(
        private readonly UserIdentityRepositoryInterface $identityRepository
    ) {
    }

    public function failureReason(): ?string {
        return $this->getErrorMessage();
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        // Accepte soit une EmailIdentity directement
        if ($candidate instanceof EmailIdentity) {
            return !$this->identityRepository->existsByIdentity($candidate);
        }

        // Soit une RegistrationRequest contenant une EmailIdentity
        if ($candidate instanceof RegistrationRequest) {
            $identity = $candidate->getIdentity();

            if ($identity instanceof EmailIdentity) {
                return !$this->identityRepository->existsByIdentity($identity);
            }
        }

        // Si ce n'est pas un email, on laisse passer
        return true;
    }

    private function getErrorMessage(): string
    {
        return 'This email address is already registered';
    }
}
