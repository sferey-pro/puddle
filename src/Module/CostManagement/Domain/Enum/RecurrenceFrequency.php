<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Enum;

use App\Core\Domain\Enum\EnumArraySerializableTrait;

/**
 * Définit les fréquences possibles pour un coût récurrent.
 */
enum RecurrenceFrequency: string
{
    use EnumArraySerializableTrait;

    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';

    public function getLabel(): string
    {
        return match ($this) {
            self::DAILY => 'Quotidien',
            self::WEEKLY => 'Hebdomadaire',
            self::MONTHLY => 'Mensuel',
        };
    }

    /**
     * Convertit le cas de l'énumération en une chaîne de caractères lisible.
     *
     * @param int|null $day le jour du mois ou de la semaine, si applicable
     *
     * @return string la description formatée
     */
    public function toHumanReadable(?int $day): string
    {
        return match ($this) {
            self::DAILY => 'Quotidien',
            self::WEEKLY => 'Hebdomadaire, chaque '.$this->getDayOfWeekName($day),
            self::MONTHLY => 'Mensuel, le '.$day.' du mois',
        };
    }

    /**
     * Petite méthode privée pour traduire le numéro du jour de la semaine en son nom.
     */
    private function getDayOfWeekName(?int $day): string
    {
        $dayMap = [
            1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi',
            5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche',
        ];

        return $dayMap[$day] ?? 'jour '.$day;
    }
}
