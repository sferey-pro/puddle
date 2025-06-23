<?php

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Repository\PasswordResetRequestRepositoryInterface;
use App\Shared\Domain\Service\ClockInterface;
use App\Shared\Domain\Service\SystemTime;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

/**
 * Gère la logique de nettoyage des anciennes demandes de réinitialisation de mot de passe.
 */
#[AsCommandHandler]
final class CleanupPasswordRequestsHandler
{
    public function __construct(
        private readonly PasswordResetRequestRepositoryInterface $repository,
        private readonly ClockInterface $clock
    ) {
    }

    /**
     * Exécute la commande de nettoyage.
     *
     * @return int Le nombre de demandes supprimées.
     */
    public function __invoke(CleanupPasswordRequests $command): int
    {
        $threshold = $this->clock->now()->modify("-{$command->daysOld} days");

        return $this->repository->deleteExpiredOlderThan($threshold);
    }
}
