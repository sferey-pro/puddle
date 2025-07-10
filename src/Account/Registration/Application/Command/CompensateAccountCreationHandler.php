<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;

use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Psr\Log\LoggerInterface;

/**
 * Gère la compensation de l'étape "Créer le compte d'authentification".
 */
#[AsCommandHandler]
final readonly class CompensateAccountCreationHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private LoggerInterface $logger,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(CompensateAccountCreation $command): void
    {

        $userAccount = $this->accountRepository->ofId($command->userId);

        if (null !== $userAccount) {
            $this->accountRepository->remove($userAccount);
            $this->em->flush();

            $this->logger->info('Account creation has been compensated by a Saga.', [
                'compensated_user_id' => (string) $command->userId,
            ]);
        }
    }
}
