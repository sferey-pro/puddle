<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Specification;

use Account\Registration\Domain\Model\RegistrationRequest;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Domain\Specification\CompositeSpecification;

/**
 * Valide que les données de la demande d'inscription sont conformes.
 *
 * RÈGLES MÉTIER :
 * - Identifiant principal présent et valide
 * - Format d'identifiant supporté
 * - Domaines email autorisés
 * - Numéros de téléphone dans les pays supportés
 * - Métadonnées obligatoires présentes
 */
final class ValidRegistrationDataSpecification extends CompositeSpecification
{
    // Configuration métier pour MVP
    private const array BLOCKED_EMAIL_DOMAINS = [
        '10minutemail.com',
        'tempmail.org',
        'guerrillamail.com',
        'mailinator.com'
    ];

    private const array SUPPORTED_PHONE_COUNTRIES = [
        '+33', // France
        '+32', // Belgique
        '+41', // Suisse
        '+1',  // US/Canada
    ];

    public function isSatisfiedBy(mixed $candidate): bool
    {
        if (!$candidate instanceof RegistrationRequest) {
            return false;
        }

        // 1. Identifiant obligatoire
        if (!$candidate->identifier) {
            return false;
        }

        // 2. Validation spécifique selon le type d'identifiant
        if (!$this->isIdentifierSupported($candidate->identifier)) {
            return false;
        }

        // 3. Métadonnées minimales présentes
        if (!$this->hasRequiredMetadata($candidate)) {
            return false;
        }

        return true;
    }

    public function failureReason(): ?string
    {
        return 'Les données d\'inscription sont incomplètes ou invalides';
    }

    private function isIdentifierSupported(mixed $identifier): bool
    {
        return match (true) {
            $identifier instanceof EmailIdentity => $this->isEmailAllowed($identifier),
            $identifier instanceof PhoneIdentity => $this->isPhoneAllowed($identifier),
            default => false
        };
    }

    private function isEmailAllowed(EmailIdentity $email): bool
    {
        $emailValue = $email->value();
        $domain = strtolower(substr($emailValue, strpos($emailValue, '@') + 1));

        // Vérifier les domaines bloqués
        if (in_array($domain, self::BLOCKED_EMAIL_DOMAINS, true)) {
            return false;
        }

        // Vérifier les patterns suspects
        if ($this->isSuspiciousEmailPattern($emailValue)) {
            return false;
        }

        return true;
    }

    private function isPhoneAllowed(PhoneIdentity $phone): bool
    {
        $phoneValue = $phone->value();

        // Vérifier que le numéro commence par un indicatif supporté
        foreach (self::SUPPORTED_PHONE_COUNTRIES as $countryCode) {
            if (str_starts_with($phoneValue, $countryCode)) {
                return true;
            }
        }

        return false;
    }

    private function hasRequiredMetadata(RegistrationRequest $request): bool
    {
        // IP address obligatoire pour la géolocalisation et sécurité
        if (!$request->ipAddress()) {
            return false;
        }

        // Validation basique de l'IP
        if (!filter_var($request->ipAddress(), FILTER_VALIDATE_IP)) {
            return false;
        }

        return true;
    }

    private function isSuspiciousEmailPattern(string $email): bool
    {
        // Pattern suspects : trop de chiffres, caractères répétés, etc.
        $localPart = substr($email, 0, strpos($email, '@'));

        // Plus de 80% de chiffres dans la partie locale
        $digitCount = preg_match_all('/\d/', $localPart);
        $digitRatio = $digitCount / strlen($localPart);

        if ($digitRatio > 0.8) {
            return true;
        }

        // Caractères répétés (ex: aaaa@example.com)
        if (preg_match('/(.)\1{3,}/', $localPart)) {
            return true;
        }

        return false;
    }
}
