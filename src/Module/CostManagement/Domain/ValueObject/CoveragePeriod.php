<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use App\Core\Application\Clock\ClockInterface;
use Webmozart\Assert\Assert;

/**
 * Représente la période de temps durant laquelle un poste de coût est pertinent ou actif.
 * Ce Value Object garantit que la date de fin est toujours postérieure à la date de début.
 */
final readonly class CoveragePeriod
{
    private \DateTimeImmutable $startDate;
    private ?\DateTimeImmutable $endDate; // Nullable si c'est un coût "continu" sans fin définie initialement

    public function __construct(\DateTimeImmutable $startDate, ?\DateTimeImmutable $endDate = null)
    {
        if (null !== $endDate) {
            Assert::greaterThan($endDate, $startDate, 'End date must be after start date.');
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Crée une nouvelle période de couverture à partir de l'horloge système et d'une durée.
     * C'est une "factory" qui simplifie la création de l'objet.
     *
     * @param ClockInterface $clock            le service d'horloge pour obtenir la date de début (maintenant)
     * @param string         $durationModifier une chaîne de modification de date valide pour DateTime::modify (ex: '+1 month')
     */
    public static function fromClock(ClockInterface $clock, string $durationModifier): self
    {
        $startDate = $clock->now();
        $endDate = $startDate->modify($durationModifier);

        return new self($startDate, $endDate);
    }

    public static function create(\DateTimeImmutable $startDate, ?\DateTimeImmutable $endDate = null): static
    {
        return new self($startDate, $endDate);
    }

    public function startDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    // public function isActive(\DateTimeImmutable $currentDate): bool
    // {
    //     $spec = new CoveragePeriodIsActiveSpecification($currentDate);

    //     return $spec->isSatisfiedBy($this);
    // }

    /**
     * Vérifie si la période de couverture est terminée par rapport à une date donnée.
     * Si la date de fin n'est pas définie (null), la période n'est jamais considérée comme terminée.
     */
    // public function isEnded(\DateTimeImmutable $currentDate): bool
    // {
    //     $spec = new CoveragePeriodHasEndedSpecification($currentDate);

    //     return $spec->isSatisfiedBy($this);
    // }

    public function equals(self $other): bool
    {
        return $this->startDate === $other->startDate && $this->endDate === $other->endDate;
    }

    // Pourrait avoir des méthodes pour "MONTHLY", "QUARTERLY" qui construisent l'objet
    public static function monthly(int $year, int $month): self
    {
        $startDate = new \DateTimeImmutable(\sprintf('%d-%02d-01', $year, $month));
        $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);

        return new self($startDate, $endDate);
    }
}
