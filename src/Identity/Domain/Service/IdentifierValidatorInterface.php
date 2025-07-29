<?php

declare(strict_types=1);

namespace Identity\Domain\Service;

/**
 * Contrat de validation des identifiants dans le domaine Identity.
 *
 * PHILOSOPHIE : Le domaine définit CE QUI doit être validé,
 * l'infrastructure décide COMMENT le valider.
 */
interface IdentifierValidatorInterface
{
    /**
     * Valide un email selon les règles métier.
     */
    public function isValidEmail(string $email): bool;

    /**
     * Valide un numéro de téléphone selon les règles métier.
     */
    public function isValidPhone(string $phone): bool;

    /**
     * Retourne les violations de validation pour un email.
     *
     * @return string[] Liste des erreurs
     */
    public function validateEmail(string $email): array;

    /**
     * Retourne les violations de validation pour un téléphone.
     *
     * @return string[] Liste des erreurs
     */
    public function validatePhone(string $phone): array;
}
