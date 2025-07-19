<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;

use Account\Core\Domain\Model\Account;
use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Account\Registration\Domain\Repository\RegistrationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Kernel\Application\Bus\EventBusInterface;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Gère l'exécution de l'étape "Créer le compte d'authentification".
 */
#[AsCommandHandler]
final class CreateAccountHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private EventBusInterface $eventBus,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(CreateAccount $command): void
    {
        $account = Account::create(
            $command->userId ?? UserId::generate(),
            $command->identifier
        );

        $this->accountRepository->save($account);
        $this->em->flush();

        $this->eventBus->publish(...$account->pullDomainEvents());
    }
}
