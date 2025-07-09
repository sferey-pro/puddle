<?php

declare(strict_types=1);

namespace App\Core\Domain\ValueObject;

/**
 * Contrat pour les Value Objects qui peuvent être vérifiés pour leur unicité.
 *
 * Un VO implémentant cette interface devient "auto-descriptif" : il sait lui-même
 * comment il doit être recherché en base de données.
 */
interface UniqueValueInterface
{
    /**
     * Retourne le chemin de la propriété Doctrine à utiliser pour la requête.
     * Exemples : 'email', etc.
     */
    public static function uniqueFieldPath(): string;

    /**
     * Retourne la valeur primitive qui doit être utilisée dans la requête.
     */
    public function uniqueValue(): mixed;
}
