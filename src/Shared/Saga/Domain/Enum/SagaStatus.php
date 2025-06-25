<?php

declare(strict_types=1);

namespace App\Shared\Saga\Domain\Enum;

use App\Core\Enum\EnumJsonSerializableTrait;

/**
 * Définit les différentes phases du cycle de vie d'un processus métier long (Saga).
 * Cela permet de savoir à tout moment si un processus est en attente, en cours,
 * terminé avec succès, en cours d'annulation (compensation) ou a échoué.
 *
 * - PENDING : Le processus a été initié mais n'a pas encore démarré
 * - RUNNING : Le processus est en cours d'exécution
 * - COMPLETED : Toutes les étapes du processus ont réussi.
 * - COMPENSATING : Une étape a échoué, le processus est en train d'annuler les étapes précédentes
 * - FAILED : Le processus a échoué et n'a pas pu (ou pas eu besoin de) compenser *
 */
enum SagaStatus: string
{
    use EnumJsonSerializableTrait;

    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case COMPENSATING = 'compensating';
    case FAILED = 'failed';

    public function equals(self $other): bool
    {
        return $this === $other;
    }
}
