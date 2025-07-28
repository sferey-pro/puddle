<?php

namespace Account\Registration\Domain\Specification;

use Account\Registration\Domain\Model\RegistrationConfig;
use Account\Registration\Domain\Model\RegistrationRequest;
use Account\Registration\Domain\Repository\RegistrationConfigRepositoryInterface;
use Kernel\Domain\Specification\SpecificationInterface;
use Kernel\Application\Clock\ClockInterface;

/**
 * Vérifie que l'inscription est ouverte selon les règles métier :
 * - Période d'inscription (dates)
 * - Limite de nouveaux comptes
 * - Mode maintenance
 * - Restrictions géographiques
 */
final class RegistrationOpenSpecification implements SpecificationInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function failureReason(): ?string {
        return null;
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {

        $config = new RegistrationConfig();

        // 1. Vérifier si les inscriptions sont activées globalement
        if (!$config->isEnabled()) {
            return false;
        }

        // 2. Vérifier la période d'inscription
        $now = $this->clock->now();
        if ($config->hasRegistrationPeriod()) {
            if ($now < $config->getStartDate() || $now > $config->getEndDate()) {
                return false;
            }
        }

        // 3. Vérifier la limite de comptes
        // if ($config->hasUserLimit()) {
        //     $currentCount = $this->configRepository->getTotalRegistrations();
        //     if ($currentCount >= $config->getUserLimit()) {
        //         return false;
        //     }
        // }

        // 4. Vérifier les restrictions géographiques (si applicable)
        if ($candidate instanceof RegistrationRequest && $config->hasGeoRestrictions()) {
            $country = $candidate->getCountry();
            if (!$config->isCountryAllowed($country)) {
                return false;
            }
        }

        return true;
    }

    private function getErrorMessage(): string
    {
        $config = $this->configRepository->getActive();

        if (!$config->isEnabled()) {
            return 'Registration is currently closed';
        }

        if ($config->hasRegistrationPeriod()) {
            $now = $this->clock->now();
            if ($now < $config->getStartDate()) {
                return sprintf(
                    'Registration will open on %s',
                    $config->getStartDate()->format('Y-m-d')
                );
            }
            if ($now > $config->getEndDate()) {
                return 'Registration period has ended';
            }
        }

        if ($config->hasUserLimit()) {
            return 'Registration limit has been reached';
        }

        return 'Registration is not available';
    }
}
