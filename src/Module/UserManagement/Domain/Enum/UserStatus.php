<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Enum;

use App\Core\Domain\Enum\EnumJsonSerializableTrait;

/**
 * Définit les différents statuts possibles pour un compte utilisateur.
 *
 * - PENDING: L'utilisateur s'est inscrit mais n'a pas encore validé son compte.
 * - ACTIVE: Le compte est actif et pleinement fonctionnel.
 * - SUSPENDED: Le compte a été désactivé par un administrateur. Il ne peut plus être utilisé.
 * - DEACTIVATED: Le compte a été désactivé, mais les données sont conservées. Il peut être réactivé.
 * - ANONYMIZED: Les données personnelles du compte ont été supprimées (RGPD). Cet état est final.
 */
enum UserStatus: string
{
    use EnumJsonSerializableTrait;

    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case DEACTIVATED = 'deactivated';
    case ANONYMIZED = 'anonymized';

    public function equals(self $other): bool
    {
        return $this === $other;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::ACTIVE => 'Actif',
            self::SUSPENDED => 'Suspendu',
            self::DEACTIVATED => 'Désactivé',
            self::ANONYMIZED => 'Anonymisé',
        };
    }

    /**
     * @return array{label: string, color: string}
     */
    public function getBadgeConfiguration(): array
    {
        return [
            'label' => $this->getLabel(),
            'color' => match ($this) {
                self::PENDING => 'yellow',
                self::ACTIVE => 'green',
                self::SUSPENDED => 'orange',
                self::DEACTIVATED => 'gray',
                self::ANONYMIZED => 'red',
            },
        ];
    }
}
