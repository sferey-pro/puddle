<?php

namespace App\Module\Auth\Application\Command;

use App\Shared\Application\Command\CommandInterface;

/**
 * Commande pour finaliser la réinitialisation du mot de passe.
 * Contient le token fourni par l'utilisateur et son nouveau mot de passe.
 */
final readonly class ResetPassword implements CommandInterface
{
    public function __construct(
        public string $token,
        #[\SensitiveParameter] public string $newPassword,
    ) {
    }
}
