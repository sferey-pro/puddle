<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Shared\Application\Command\CommandInterface;

/**
 * Commande pour démarrer le processus d'inscription utilisateur (Saga).
 *
 * Elle est créée par la couche UI (contrôleur) avec les données brutes
 * du formulaire d'inscription. Son seul rôle est de déclencher la RegistrationSaga.
 */
final readonly class StartRegistrationSaga implements CommandInterface
{
    public function __construct(
        public Email $email,
        public Password $plainPassword,
    ) {
    }
}
