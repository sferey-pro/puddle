<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain\Enum;

use App\Core\Domain\Enum\EnumArraySerializableTrait;

/**
 * Représente les différents statuts qu'une commande (Order) peut avoir.
 *
 * - PENDING : La commande est en attente de confirmation.
 * - CONFIRMED : La commande a été confirmée et est en cours de traitement.
 * - CANCELLED : La commande a été annulée par l'utilisateur.
 * - FAILED : La commande a échoué et ne peut plus être traitée.
 * - PAID : La commande a été payée et est prête à être livrée.
 */
enum OrderStatus: string
{
    use EnumArraySerializableTrait;

    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';
    case PAID = 'paid';

    public function equals(self $other): bool
    {
        return $this === $other;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::CANCELLED => 'Cancelled',
            self::FAILED => 'Failed',
            self::PAID => 'Paid',
            default => 'Unknown',
        };
    }

    /**
     * @return array{label: string, color: string, dot: bool}
     */
    public function getBadgeConfiguration(): array
    {
        return [
            'label' => $this->getLabel(),
            'color' => match ($this) {
                self::PENDING => 'blue',
                self::CONFIRMED => 'green',
                self::CANCELLED => 'red',
                self::FAILED => 'red',
                self::PAID => 'green',
                default => 'gray',
            },
        ];
    }
}
