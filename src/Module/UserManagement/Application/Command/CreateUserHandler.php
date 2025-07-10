<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Exception\UserException;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Gère la commande de création d'un utilisateur dans le module UserManagement.
 *
 * Il lève une exception `UserException` si la logique métier (comme l'unicité de l'email)
 * est violée, ce qui provoquera l'échec de l'étape du Saga.
 */
#[AsCommandHandler]
final class CreateUserHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $userRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(CreateUser $command): void
    {
        $identity = $command->identity;


        $spec = new IsUniqueSpecification($identity);
        if (0 !== $this->userRepository->countBySpecification($spec)) {
            throw UserException::identityAlreadyInUse($identity);
        }

        $user = User::create(
            $command->userId ?? UserId::generate(),
            $identity
        );

        $this->userRepository->add($user);
        $this->em->flush();

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
