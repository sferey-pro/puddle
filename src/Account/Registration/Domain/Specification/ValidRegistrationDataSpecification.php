<?php

namespace Account\Registration\Domain\Specification;

use Kernel\Domain\Specification\CompositeSpecification;
use Identity\Domain\ValueObject\Identifier;

/**
 * Valide les données complètes d'inscription :
 * - Format et validité de l'identifiant
 * - Données additionnelles requises
 * - Cohérence des informations
 * - Règles métier spécifiques
 */
final class ValidRegistrationDataSpecification extends CompositeSpecification
{
    public function __construct(
        private readonly IdentityFormatSpecification $identityFormat,
        private readonly ProhibitedPatternsSpecification $prohibitedPatterns,
        private readonly BusinessRulesSpecification $businessRules
    ) {
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        if (!$candidate instanceof RegistrationRequest) {
            return false;
        }

        // 1. Valider le format de base
        if (!$this->identityFormat->isSatisfiedBy($candidate->getIdentity())) {
            return false;
        }

        // 2. Vérifier les patterns interdits (spam, test accounts, etc.)
        if (!$this->prohibitedPatterns->isSatisfiedBy($candidate)) {
            return false;
        }

        // 3. Appliquer les règles métier
        if (!$this->businessRules->isSatisfiedBy($candidate)) {
            return false;
        }

        // 4. Validation spécifique selon le type d'identité
        return match (true) {
            $candidate->getIdentity() instanceof EmailIdentity =>
                $this->validateEmailRegistration($candidate),
            $candidate->getIdentity() instanceof PhoneIdentity =>
                $this->validatePhoneRegistration($candidate),
            default => false
        };
    }

    private function validateEmailRegistration(RegistrationRequest $request): bool
    {
        $email = $request->getIdentity()->getValue();

        // Pas d'adresses temporaires
        $tempEmailDomains = ['tempmail.com', '10minutemail.com', 'guerrillamail.com'];
        $domain = substr($email, strrpos($email, '@') + 1);

        if (in_array($domain, $tempEmailDomains)) {
            return false;
        }

        // Pas de patterns suspects
        if (preg_match('/test\d+@/', $email)) {
            return false;
        }

        return true;
    }

    private function validatePhoneRegistration(RegistrationRequest $request): bool
    {
        $phone = $request->getIdentity()->getValue();

        // Vérifier que ce n'est pas un numéro de test
        $testNumbers = ['+33000000000', '+1234567890'];

        if (in_array($phone, $testNumbers)) {
            return false;
        }

        // Vérifier le format international
        if (!preg_match('/^\+[1-9]\d{1,14}$/', $phone)) {
            return false;
        }

        return true;
    }
}
