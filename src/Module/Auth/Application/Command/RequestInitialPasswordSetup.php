<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Command\CommandInterface;

/**
 * Rôle : Commande pour demander l'envoi d'un e-mail d'invitation
 * à un nouvel utilisateur afin qu'il configure son mot de passe.
 */
final readonly class RequestInitialPasswordSetup implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public Email $email
    ) {}
}
