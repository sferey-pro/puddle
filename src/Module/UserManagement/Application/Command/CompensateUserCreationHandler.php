<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Gère la commande de compensation pour la création du profil utilisateur.
 */
#[AsCommandHandler]
final readonly class CompensateUserCreationHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(CompensateUserCreation $command): void
    {
        $userAccount = $this->userRepository->ofId($command->userId);

        if (null !== $userAccount) {
            $this->userRepository->remove($userAccount);
            $this->em->flush();

            $this->logger->info('UserAccount creation has been compensated by a Saga.', [
                'compensated_user_id' => (string) $command->userId,
            ]);
        }
    }
}
