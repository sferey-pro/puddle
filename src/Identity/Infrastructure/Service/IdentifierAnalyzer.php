<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Service;

use Identity\Domain\Service\IdentifierValidatorInterface;
use SharedKernel\Domain\DTO\Identity\IdentifierAnalysis;
use SharedKernel\Domain\Service\IdentifierAnalyzerInterface;

/**
 * Implémentation de l'analyseur d'identifiants.
 *
 * RESPONSABILITÉS :
 * - Déterminer le type d'un identifiant
 * - Valider son format
 * - Normaliser sa valeur
 * - Créer une version masquée pour l'affichage
 * - Déterminer le canal de communication approprié
 *
 * PLACEMENT : Dans Identity car c'est la logique métier des identifiants.
 */
final class IdentifierAnalyzer implements IdentifierAnalyzerInterface
{
    public function __construct(
        private readonly IdentifierValidatorInterface $validator
    ) {}

    public function analyze(string $rawIdentifier): IdentifierAnalysis
    {
        // Nettoyer l'input
        $trimmed = trim($rawIdentifier);

        if (empty($trimmed)) {
            return IdentifierAnalysis::invalid(
                rawValue: $rawIdentifier,
                errorMessage: 'Please enter an email address or phone number.'
            );
        }

        // Tenter d'identifier et valider comme email
        if ($this->looksLikeEmail($trimmed)) {
            return $this->analyzeAsEmail($trimmed);
        }

        // Tenter d'identifier et valider comme téléphone
        if ($this->looksLikePhone($trimmed)) {
            return $this->analyzeAsPhone($trimmed);
        }

        // Format non reconnu
        return IdentifierAnalysis::invalid(
            rawValue: $rawIdentifier,
            errorMessage: 'Please enter a valid email address or phone number.'
        );
    }

    private function analyzeAsEmail(string $email): IdentifierAnalysis
    {
        // Utiliser le validator Symfony
        $errors = $this->validator->validateEmail($email);

        if (!empty($errors)) {
            return IdentifierAnalysis::invalid(
                rawValue: $email,
                errorMessage: $errors[0] // Première erreur
            );
        }

        // Email valide - normaliser et masquer
        $normalized = strtolower(trim($email));
        $masked = $this->maskEmail($normalized);

        return IdentifierAnalysis::validEmail(
            rawValue: $email,
            normalizedValue: $normalized,
            maskedValue: $masked
        );
    }

    private function analyzeAsPhone(string $phone): IdentifierAnalysis
    {
        // Nettoyer pour la validation
        $cleaned = preg_replace('/[^+0-9]/', '', $phone);

        // Utiliser le validator Symfony
        $errors = $this->validator->validatePhone($cleaned);

        if (!empty($errors)) {
            return IdentifierAnalysis::invalid(
                rawValue: $phone,
                errorMessage: $errors[0] // Première erreur
            );
        }

        // Téléphone valide - normaliser et masquer
        $normalized = $this->normalizePhone($cleaned);
        $masked = $this->maskPhone($normalized);
        $countryCode = $this->extractCountryCode($normalized);

        return IdentifierAnalysis::validPhone(
            rawValue: $phone,
            normalizedValue: $normalized,
            maskedValue: $masked,
            countryCode: $countryCode
        );
    }

    private function looksLikeEmail(string $identifier): bool
    {
        // Contient @ et au moins un point après
        return str_contains($identifier, '@')
            && str_contains(substr($identifier, strpos($identifier, '@')), '.');
    }

    private function looksLikePhone(string $identifier): bool
    {
        // Contient principalement des chiffres, peut commencer par +
        $cleaned = preg_replace('/[^+0-9]/', '', $identifier);
        return strlen($cleaned) >= 8 && preg_match('/^[+0-9]+$/', $cleaned);
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '***@***.***';
        }

        [$localPart, $domain] = $parts;

        // Masquer intelligemment selon la longueur
        $localLength = strlen($localPart);
        $maskedLocal = match (true) {
            $localLength <= 2 => str_repeat('*', $localLength),
            $localLength <= 4 => substr($localPart, 0, 1) . str_repeat('*', $localLength - 1),
            default => substr($localPart, 0, 2) . str_repeat('*', min(4, $localLength - 2))
        };

        // Masquer partiellement le domaine
        $domainParts = explode('.', $domain);
        if (count($domainParts) >= 2 && strlen($domainParts[0]) > 3) {
            $domainParts[0] = substr($domainParts[0], 0, 2) . '***';
        }

        return $maskedLocal . '@' . implode('.', $domainParts);
    }

    private function maskPhone(string $phone): string
    {
        $length = strlen($phone);

        if ($length < 6) {
            return str_repeat('*', $length);
        }

        // Format international
        if (str_starts_with($phone, '+')) {
            // +33 6** ** **89
            $countryCodeLength = $this->getCountryCodeLength($phone);
            $visibleStart = substr($phone, 0, $countryCodeLength + 2); // +33 6
            $visibleEnd = substr($phone, -2); // 89
            $hiddenLength = $length - strlen($visibleStart) - 2;

            return $visibleStart . str_repeat('*', $hiddenLength) . $visibleEnd;
        }

        // Format national
        return substr($phone, 0, 2) . str_repeat('*', $length - 4) . substr($phone, -2);
    }

    private function normalizePhone(string $phone): string
    {
        // Si pas de préfixe international, ajouter celui par défaut
        if (!str_starts_with($phone, '+')) {
            // Pour la France (configurable)
            if (str_starts_with($phone, '0')) {
                return '+33' . substr($phone, 1);
            }
            return '+33' . $phone;
        }

        return $phone;
    }

    private function extractCountryCode(string $normalizedPhone): ?string
    {
        if (!str_starts_with($normalizedPhone, '+')) {
            return null;
        }

        // Map simple des codes pays
        $countryMap = [
            '+1' => 'US',      // USA/Canada
            '+33' => 'FR',     // France
            '+44' => 'GB',     // UK
            '+49' => 'DE',     // Germany
            '+34' => 'ES',     // Spain
            '+39' => 'IT',     // Italy
            '+41' => 'CH',     // Switzerland
            '+32' => 'BE',     // Belgium
            '+31' => 'NL',     // Netherlands
            '+352' => 'LU',    // Luxembourg
        ];

        foreach ($countryMap as $prefix => $country) {
            if (str_starts_with($normalizedPhone, $prefix)) {
                return $country;
            }
        }

        return 'UNKNOWN';
    }

    private function getCountryCodeLength(string $phone): int
    {
        // Longueurs des codes pays courants
        $lengths = [
            1 => ['+1', '+7'],                    // USA, Russia
            2 => ['+20', '+27', '+30' /* ... */], // Egypt, South Africa, Greece
            3 => ['+33', '+34', '+39' /* ... */], // Most European countries
        ];

        foreach ([3, 2, 1] as $length) {
            $prefix = substr($phone, 0, $length + 1);
            foreach ($lengths[$length] ?? [] as $code) {
                if (str_starts_with($prefix, $code)) {
                    return $length + 1;
                }
            }
        }

        return 3; // Par défaut
    }
}
