<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use App\Module\CostManagement\Domain\Enum\RecurrenceFrequency;
use Cron\CronExpression;
use InvalidArgumentException;

/**
 * Représente la règle de périodicité d'un coût récurrent.
 * Cet objet de valeur abstrait la complexité des expressions CRON
 * en fournissant des méthodes de construction simples (ex: mensuel, quotidien).
 */
final readonly class RecurrenceRule
{
    /**
     * @param RecurrenceFrequency $frequency La fréquence (quotidien, hedbomadaire, mensuel).
     * @param int|null $day Le jour de la semaine (1-7) ou du mois (1-31) selon la fréquence.
     * @param string $rule L'expression CRON générée.
     */
    private function __construct(
        public RecurrenceFrequency $frequency,
        public ?int $day,
        private string $rule
    ) {
    }

    /**
     * Crée une règle de récurrence pour une exécution mensuelle à un jour donné.
     *
     * @param int $dayOfMonth Le jour du mois (entre 1 et 31).
     */
    public static function monthlyOn(int $dayOfMonth): self
    {
        if ($dayOfMonth < 1 || $dayOfMonth > 31) throw new InvalidArgumentException('Le jour du mois doit être compris entre 1 et 31.');
        return new self(RecurrenceFrequency::MONTHLY, $dayOfMonth, sprintf('0 0 %d * *', $dayOfMonth));
    }

    /**
     * Crée une règle de récurrence pour une exécution hebdomadaire à un jour donné.
     *
     * @param int $dayOfWeek Le jour de la semaine (1 pour Lundi, 7 pour Dimanche).
     */
    public static function weeklyOn(int $dayOfWeek): self
    {
        if ($dayOfWeek < 1 || $dayOfWeek > 7) throw new InvalidArgumentException('Le jour de la semaine doit être compris entre 1 (Lundi) et 7 (Dimanche).');
        return new self(RecurrenceFrequency::WEEKLY, $dayOfWeek, sprintf('0 0 * * %d', $dayOfWeek));
    }

    /**
     * Crée une règle de récurrence pour une exécution quotidienne.
     */
    public static function daily(): self
    {
        return new self(RecurrenceFrequency::DAILY, null, '0 0 * * *');
    }

    /**
     * Retourne la chaîne de modification de date correspondant à la fréquence.
     * Exemples : '+1 month', '+1 week'.
     */
    public function getDurationModifier(): string
    {
        return match ($this->frequency) {
            RecurrenceFrequency::DAILY => '+1 day',
            RecurrenceFrequency::WEEKLY => '+1 week',
            RecurrenceFrequency::MONTHLY => '+1 month',
            // Note : Pour des cas plus complexes comme "tous les 15 jours",
            // il faudrait enrichir cette logique. Pour l'instant, cela couvre les cas standards.
        };
    }

    /**
     * Calcule la prochaine date d'exécution de la règle à partir d'un point de référence.
     *
     * @param DateTimeImmutable $from La date à partir de laquelle calculer la prochaine échéance.
     * @return DateTimeImmutable
     */
    public function getNextRunDate(\DateTimeImmutable $from): \DateTimeImmutable
    {
        $cron = new CronExpression($this->rule);
        return \DateTimeImmutable::createFromMutable($cron->getNextRunDate($from));
    }

    /**
     * Vérifie si la règle de récurrence est due à la date et heure fournies.
     *
     * @param \DateTimeInterface $dateTime
     * @return bool
     */
    public function isDue(\DateTimeInterface $dateTime = new \DateTimeImmutable()): bool
    {
        $cron = new CronExpression($this->rule);
        return $cron->isDue($dateTime);
    }

    /**
     * Retourne l'expression CRON sous forme de chaîne.
     * Utile pour la persistance en base de données.
     */
    public function __toString(): string
    {
        return $this->rule;
    }
}
