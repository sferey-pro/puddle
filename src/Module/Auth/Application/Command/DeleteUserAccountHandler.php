<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

/**
 * Handler pour la suppression de UserAccount.
 */
#[AsCommandHandler]
final readonly class DeleteUserAccountHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(DeleteUserAccount $command): void
    {
        $userAccount = $this->userRepository->ofId($command->userId);
        if ($userAccount) {
            $this->userRepository->remove($userAccount, true);
        }
    }
}
