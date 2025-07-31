<?php

declare(strict_types=1);

namespace Authentication\Domain\Specification;

use Account\Core\Domain\Model\Account;
use Account\Lifecycle\Domain\Model\State\SuspendedState;
use Authentication\Domain\Model\LoginRequest;
use Kernel\Application\Clock\ClockInterface;
use Kernel\Domain\Specification\CompositeSpecification;

/**
 * Détermine si un compte peut se connecter selon les règles métier.
 *
 * RÈGLES MÉTIER :
 * - Compte actif (pas suspendu, banni, supprimé)
 * - Compte vérifié (si vérification obligatoire)
 * - Pas de restriction temporaire
 * - Respect des conditions d'utilisation
 */
final class AccountCanLoginSpecification extends CompositeSpecification
{
    // Configuration métier pour MVP
    private const bool EMAIL_VERIFICATION_REQUIRED = false; // Pour MVP, pas obligatoire
    private const int MAX_SUSPENSION_DAYS = 30;
    private const array BLOCKED_ACCOUNT_STATES = ['banned', 'deleted', 'fraud'];

    public function __construct(
        private readonly ClockInterface $clock,
    ) {}

    public function isSatisfiedBy(mixed $candidate): bool
    {
        if (!$candidate instanceof LoginRequest) {
            return false;
        }

        $account = $candidate->account;

        // 1. Compte doit exister
        if (!$account) {
            return false;
        }

        // 2. État du compte
        if (!$this->isAccountStateValid($account)) {
            return false;
        }

        // 3. Vérification email (si requise)
        if (self::EMAIL_VERIFICATION_REQUIRED && !$this->isAccountVerified($account)) {
            return false;
        }

        // 4. Suspension temporaire expirée
        if (!$this->isSuspensionExpired($account)) {
            return false;
        }

        // 5. Règles temporelles (heures d'accès, jours de la semaine)
        if (!$this->isLoginAllowedAtThisTime()) {
            return false;
        }

        return true;
    }

    public function failureReason(): ?string
    {
        // Note: Dans la vraie implémentation, on stockerait le dernier échec
        return 'Connexion non autorisée pour ce compte';
    }

    private function isAccountStateValid(Account $account): bool
    {
        $status = $account->getState()->getName();

        // États bloqués définitivement
        if (in_array($status, self::BLOCKED_ACCOUNT_STATES, true)) {
            return false;
        }

        // Compte doit être actif
        if ($status !== 'active' && $status !== 'suspended') {
            return false;
        }

        return true;
    }

    private function isAccountVerified(Account $account): bool
    {
        // Pour MVP : on considère que si l'account existe, il est "suffisamment" vérifié
        // Dans une vraie implémentation, on vérifierait account->isEmailVerified()
        return $account->getCreatedAt() !== null;
    }

    private function isSuspensionExpired(Account $account): bool
    {
        if ($account->getState()->getName() !== 'suspended') {
            return true; // Pas suspendu
        }

        $suspendedAt = $account->getSuspendedAt();
        if (!$suspendedAt) {
            return true; // Pas de date de suspension
        }

        $now = $this->clock->now();
        $suspensionEnd = $suspendedAt->modify('+' . self::MAX_SUSPENSION_DAYS . ' days');

        return $now > $suspensionEnd;
    }

    private function isLoginAllowedAtThisTime(): bool
    {
        $now = $this->clock->now();

        // Exemple : Pas de connexion entre 2h et 6h du matin (maintenance)
        $hour = (int) $now->format('H');
        if ($hour >= 2 && $hour <= 6) {
            return false;
        }

        // Exemple : Service disponible 24/7 sauf restriction maintenance
        return true;
    }
}
