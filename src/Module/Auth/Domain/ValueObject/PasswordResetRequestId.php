<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'une demande de réinitialisation de mot de passe.
 * Il garantit que chaque identifiant est un UUID valide et immuable.
 */
final class PasswordResetRequestId extends AggregateRootId
{
}
