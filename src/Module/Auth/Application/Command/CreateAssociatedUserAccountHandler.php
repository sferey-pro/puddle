<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final readonly class CreateAssociatedUserAccountHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateAssociatedUserAccount $command): void
    {
        $userId = $command->userId;
        $email = $command->email;

        if ($this->userRepository->ofId($userId)) {
            return;
        }

        // Crée le UserAccount sans mot de passe, l'utilisateur devra peut-être
        // utiliser la fonction "mot de passe oublié" pour son premier accès.
        $userAccount = UserAccount::createAssociated($userId, $email);

        $this->userRepository->save($userAccount, true);

        $this->eventBus->publish(...$userAccount->pullDomainEvents());
    }
}
