<?php

declare(strict_types=1);

namespace Identity\Domain\Specification;

use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\Identifier;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Domain\Specification\CompositeSpecification;

/**
 * Valide le format technique d'un identifiant selon les règles métier strictes.
 *
 * RÈGLES MÉTIER :
 * - Email : Format RFC 5322 + règles business
 * - Phone : Format international E.164 + longueur
 * - Caractères autorisés et interdits
 * - Longueurs min/max selon le type
 */
final class IdentifierFormatIsValidSpecification extends CompositeSpecification
{
    // Configuration métier pour formats
    private const int EMAIL_MIN_LENGTH = 5;  // a@b.c
    private const int EMAIL_MAX_LENGTH = 254; // RFC 5321
    private const int PHONE_MIN_LENGTH = 8;   // +1234567
    private const int PHONE_MAX_LENGTH = 17;  // +12345678901234567

    // Patterns regex pour validation stricte
    private const string EMAIL_PATTERN = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    private const string PHONE_PATTERN = '/^\+[1-9]\d{6,15}$/'; // E.164 format

    // Caractères interdits dans les emails (sécurité)
    private const array EMAIL_FORBIDDEN_CHARS = ['<', '>', '"', "'", '\\', '`'];

    public function isSatisfiedBy(mixed $candidate): bool
    {
        if (!$candidate instanceof Identifier) {
            return false;
        }

        return match (true) {
            $candidate instanceof EmailIdentity => $this->isValidEmailFormat($candidate),
            $candidate instanceof PhoneIdentity => $this->isValidPhoneFormat($candidate),
            default => false
        };
    }

    public function failureReason(): ?string
    {
        return 'Le format de l\'identifiant n\'est pas valide';
    }

    private function isValidEmailFormat(EmailIdentity $email): bool
    {
        $value = $email->value();

        // 1. Longueur
        if (strlen($value) < self::EMAIL_MIN_LENGTH || strlen($value) > self::EMAIL_MAX_LENGTH) {
            return false;
        }

        // 2. Pattern de base
        if (!preg_match(self::EMAIL_PATTERN, $value)) {
            return false;
        }

        // 3. Caractères interdits
        foreach (self::EMAIL_FORBIDDEN_CHARS as $forbiddenChar) {
            if (str_contains($value, $forbiddenChar)) {
                return false;
            }
        }

        // 4. Validation des parties
        [$localPart, $domainPart] = explode('@', $value);

        if (!$this->isValidEmailLocalPart($localPart)) {
            return false;
        }

        if (!$this->isValidEmailDomainPart($domainPart)) {
            return false;
        }

        return true;
    }

    private function isValidPhoneFormat(PhoneIdentity $phone): bool
    {
        $value = $phone->value();

        // 1. Longueur
        if (strlen($value) < self::PHONE_MIN_LENGTH || strlen($value) > self::PHONE_MAX_LENGTH) {
            return false;
        }

        // 2. Pattern E.164 strict
        if (!preg_match(self::PHONE_PATTERN, $value)) {
            return false;
        }

        // 3. Validation des indicatifs connus
        if (!$this->hasValidCountryCode($value)) {
            return false;
        }

        // 4. Pas de séquences suspectes
        if ($this->hasSuspiciousPhonePattern($value)) {
            return false;
        }

        return true;
    }

    private function isValidEmailLocalPart(string $localPart): bool
    {
        // Ne peut pas commencer ou finir par un point
        if (str_starts_with($localPart, '.') || str_ends_with($localPart, '.')) {
            return false;
        }

        // Pas de points consécutifs
        if (str_contains($localPart, '..')) {
            return false;
        }

        // Longueur max de la partie locale (RFC 5321)
        if (strlen($localPart) > 64) {
            return false;
        }

        return true;
    }

    private function isValidEmailDomainPart(string $domainPart): bool
    {
        // Longueur max du domaine
        if (strlen($domainPart) > 253) {
            return false;
        }

        // Au moins un point (TLD obligatoire)
        if (!str_contains($domainPart, '.')) {
            return false;
        }

        // Validation basique du format domaine
        if (!preg_match('/^[a-zA-Z0-9.-]+$/', $domainPart)) {
            return false;
        }

        // Ne peut pas commencer ou finir par un tiret ou point
        if (preg_match('/^[-.]|[-.]$/', $domainPart)) {
            return false;
        }

        // TLD d'au moins 2 caractères
        $tld = substr($domainPart, strrpos($domainPart, '.') + 1);
        if (strlen($tld) < 2) {
            return false;
        }

        return true;
    }

    private function hasValidCountryCode(string $phone): bool
    {
        // Liste des indicatifs pays les plus courants pour MVP
        $validCountryCodes = [
            '+1',   // US/Canada
            '+33',  // France
            '+32',  // Belgique
            '+41',  // Suisse
            '+44',  // UK
            '+49',  // Allemagne
            '+39',  // Italie
            '+34',  // Espagne
            '+31',  // Pays-Bas
            '+46',  // Suède
            '+47',  // Norvège
            '+45',  // Danemark
            '+358', // Finlande
            '+351', // Portugal
            '+43',  // Autriche
            '+420', // République Tchèque
            '+48',  // Pologne
        ];

        foreach ($validCountryCodes as $code) {
            if (str_starts_with($phone, $code)) {
                return true;
            }
        }

        return false;
    }

    private function hasSuspiciousPhonePattern(string $phone): bool
    {
        // Retire l'indicatif pour analyser le numéro national
        $numberPart = substr($phone, strpos($phone, '+') + 1);

        // Trop de chiffres identiques consécutifs (ex: +33111111111)
        if (preg_match('/(\d)\1{4,}/', $numberPart)) {
            return true;
        }

        // Séquences croissantes/décroissantes suspectes (ex: +33123456789)
        if (preg_match('/(?:0123456789|9876543210)/', $numberPart)) {
            return true;
        }

        // Numéros de test connus
        $testNumbers = ['1234567890', '0000000000', '1111111111'];
        foreach ($testNumbers as $testNumber) {
            if (str_contains($numberPart, $testNumber)) {
                return true;
            }
        }

        return false;
    }
}
