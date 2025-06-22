<?php

namespace App\Module\Auth\Application\Command;

use App\Shared\Application\Command\CommandInterface;

/**
 * Commande pour initier le processus de réinitialisation de mot de passe.
 * Contient l'email de l'utilisateur qui a fait la demande.
 */
final readonly class RequestPasswordReset implements CommandInterface
{
    public function __construct(
        public string $email,
        public string $ipAddress,
    ) {
    }
}
