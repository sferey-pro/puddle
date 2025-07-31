<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Specification;

use Account\Registration\Domain\Model\RegistrationRequest;
use Kernel\Application\Clock\ClockInterface;
use Kernel\Domain\Specification\CompositeSpecification;

/**
 * Vérifie que l'inscription est ouverte selon les règles métier pures.
 *
 * RÈGLES MÉTIER :
 * - Inscriptions globalement activées
 * - Dans la période d'inscription (si configurée)
 * - Pays autorisé (si restriction géographique)
 * - Pas en période de maintenance
 */
final class RegistrationIsOpenSpecification extends CompositeSpecification
{
    // Configuration métier en dur pour MVP (pas d'infrastructure)
    private const bool REGISTRATION_ENABLED = true;
    private const array ALLOWED_COUNTRIES = ['FR', 'BE', 'CH', 'CA']; // null = tous autorisés
    private const array MAINTENANCE_HOURS = []; // Format : ['02:00-04:00'] en UTC

    public function __construct(
        private readonly ClockInterface $clock,
    ) {}

    public function isSatisfiedBy(mixed $candidate): bool
    {
        if (!$candidate instanceof RegistrationRequest) {
            return false;
        }

        // 1. Inscriptions globalement désactivées
        if (!self::REGISTRATION_ENABLED) {
            return false;
        }

        // 2. Vérification période de maintenance
        if ($this->isInMaintenanceWindow()) {
            return false;
        }

        // 3. Restriction géographique (si configurée)
        if (!empty(self::ALLOWED_COUNTRIES)) {
            $country = $candidate->getCountry();
            if ($country && !in_array($country, self::ALLOWED_COUNTRIES, true)) {
                return false;
            }
        }

        // 4. Vérifications temporelles (weekend, heures ouvrées, etc.)
        if ($this->isRegistrationRestrictedByTime()) {
            return false;
        }

        return true;
    }

    public function failureReason(): ?string
    {
        if (!self::REGISTRATION_ENABLED) {
            return 'Les inscriptions sont temporairement fermées';
        }

        if ($this->isInMaintenanceWindow()) {
            return 'Service en maintenance, réessayez dans quelques minutes';
        }

        if ($this->isRegistrationRestrictedByTime()) {
            return 'Les inscriptions sont suspendues temporairement';
        }

        return 'Inscription non autorisée depuis votre localisation';
    }

    private function isInMaintenanceWindow(): bool
    {
        if (empty(self::MAINTENANCE_HOURS)) {
            return false;
        }

        $now = $this->clock->now();
        $currentTime = $now->format('H:i');

        foreach (self::MAINTENANCE_HOURS as $window) {
            [$start, $end] = explode('-', $window);
            if ($currentTime >= $start && $currentTime <= $end) {
                return true;
            }
        }

        return false;
    }

    private function isRegistrationRestrictedByTime(): bool
    {
        $now = $this->clock->now();

        // Exemple : Pas d'inscription le dimanche (jour 0)
        if ($now->format('w') === '0') {
            return true;
        }

        // Exemple : Pas d'inscription entre 1h et 6h du matin
        $hour = (int) $now->format('H');
        if ($hour >= 1 && $hour <= 6) {
            return true;
        }

        return false;
    }
}
