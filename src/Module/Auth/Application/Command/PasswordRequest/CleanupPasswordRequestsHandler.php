<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\PasswordRequest;

use App\Core\Application\Clock\ClockInterface;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Domain\Repository\PasswordResetRequestRepositoryInterface;

/**
 * Orchestre le cas d'usage "Nettoyer les anciennes demandes de réinitialisation".
 *
 * Ce handler est responsable de transformer le paramètre "days-old" en une date
 * de seuil concrète et d'appeler le repository pour effectuer la suppression.
 */
#[AsCommandHandler]
final class CleanupPasswordRequestsHandler
{
    public function __construct(
        private readonly PasswordResetRequestRepositoryInterface $repository,
        private readonly ClockInterface $clock,
    ) {
    }

    /**
     * Exécute la logique de nettoyage.
     *
     * @return int le nombre de demandes qui ont été supprimées
     */
    public function __invoke(CleanupPasswordRequests $command): int
    {
        // Le handler calcule la date seuil en se basant sur le temps "présent" fourni par le Clock.
        $threshold = $this->clock->now()->modify("-{$command->daysOld} days");

        return $this->repository->deleteExpiredOlderThan($threshold);
    }
}
