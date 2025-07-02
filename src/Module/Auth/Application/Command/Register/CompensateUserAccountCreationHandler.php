<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Gère la compensation de l'étape "Créer le compte d'authentification".
 */
#[AsCommandHandler]
final readonly class CompensateUserAccountCreationHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(CompensateUserAccountCreation $command): void
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
