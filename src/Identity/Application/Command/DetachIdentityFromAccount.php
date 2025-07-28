<?php

declare(strict_types=1);

namespace Identity\Application\Command;

use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Message\CommandInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Command pour détacher un identifiant d'un compte.
 * Utilisée pour les opérations de suppression d'identité.
 */
final readonly class DetachIdentityFromAccount implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public Identifier $identifier,
        public bool $forceRemovePrimary = false, // Sécurité : interdire par défaut la suppression de l'identité primaire
    ) {
    }
}
