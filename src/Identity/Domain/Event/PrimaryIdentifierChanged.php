<?php

declare(strict_types=1);

namespace Identity\Domain\Event;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Event\DomainEvent;

/**
 * Événement émis lorsque l'identifiant principal d'un utilisateur change.
 *
 * Cet événement est critique car il peut impacter :
 * - Les notifications (nouvel identifiant pour les communications)
 * - L'authentification (changement du moyen de connexion principal)
 * - L'audit trail (traçabilité des changements d'identité)
 */
final readonly class PrimaryIdentifierChanged extends DomainEvent
{
    /**
     * @param UserId $userId L'ID de l'utilisateur concerné
     * @param Identifier $previousPrimary L'ancien identifiant principal
     * @param Identifier $newPrimary Le nouvel identifiant principal
     * @param string $reason Raison du changement (user_request, admin_action, verification_completed, etc.)
     */
    public function __construct(
        private(set) UserId $userId,
        private(set) Identifier $previousPrimary,
        private(set) Identifier $newPrimary,
        private(set) string $reason
    ) {
        parent::__construct($userId);
    }

    /**
     * {@inheritDoc}
     */
    public static function eventName(): string
    {
        return 'identity.identifier.primary_changed';
    }
}
