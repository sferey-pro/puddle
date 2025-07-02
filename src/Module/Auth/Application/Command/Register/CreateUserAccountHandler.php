<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Domain\Exception\UserException;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Gère l'exécution de l'étape "Créer le compte d'authentification".
 */
#[AsCommandHandler]
final class CreateUserAccountHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EventBusInterface $eventBus,
        private EventDispatcherInterface $eventDispatcher,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(CreateUserAccount $command): void
    {
        $spec = new IsUniqueSpecification($command->email);

        if (0 !== $this->userRepository->countBySpecification($spec)) {
            throw UserException::emailAlreadyExists($command->email);
        }

        $user = UserAccount::create($command->userId ?? UserId::generate(), $command->email);

        $this->userRepository->add($user);
        $this->em->flush();

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
