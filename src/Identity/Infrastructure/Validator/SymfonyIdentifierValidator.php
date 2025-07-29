<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Validator;

use Identity\Domain\Service\IdentifierValidatorInterface;
use Identity\Infrastructure\Validator\Constraint\DisposableEmailDomain;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Implémentation du validator utilisant le composant Symfony Validator.
 *
 * AVANTAGES :
 * - Réutilise la logique éprouvée de Symfony
 * - Facilement extensible avec des contraintes custom
 * - Messages d'erreur internationalisables
 * - Validation complexe possible (DNS check pour email, etc.)
 */
final class SymfonyIdentifierValidator implements IdentifierValidatorInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {}

    public function isValidEmail(string $email): bool
    {
        return count($this->validateEmail($email)) === 0;
    }

    public function isValidPhone(string $phone): bool
    {
        return count($this->validatePhone($phone)) === 0;
    }

    public function validateEmail(string $email): array
    {
        $violations = $this->validator->validate($email, [
            new Assert\NotBlank(
                message: 'Email cannot be empty.',),
            new Assert\Email(
                message: 'The email "{{ value }}" is not a valid email address.',
                mode: Assert\Email::VALIDATION_MODE_STRICT, // Vérifie aussi le DNS
            ),
            new Assert\Length(
                max: 255,
                maxMessage: 'Email cannot be longer than {{ limit }} characters.',
            ),
            // Contrainte custom pour exclure les domaines jetables
            new DisposableEmailDomain(
                message: 'Disposable email addresses are not allowed.'
            ),
        ]);

        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }

    public function validatePhone(string $phone): array
    {
        // Nettoyer le numéro pour la validation
        $cleaned = preg_replace('/[^+0-9]/', '', $phone);

        $violations = $this->validator->validate($cleaned, [
            new Assert\NotBlank(
                message: 'Phone number cannot be empty.',
            ),
            new Assert\Regex(
                pattern: '/^\+?[0-9]{8,15}$/',
                message: 'Phone number must be between 8 and 15 digits.',
            ),
            // Contrainte custom pour valider le format international
            // new InternationalPhoneNumber(
            //     message: 'Please enter a valid international phone number.',
            //     defaultRegion: 'FR', // Région par défaut si pas de préfixe
            // ),
        ]);

        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }
}
