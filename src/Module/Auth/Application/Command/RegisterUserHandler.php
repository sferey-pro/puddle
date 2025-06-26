<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Domain\Exception\UserException;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Gère la commande d'enregistrement d'un nouvel utilisateur.
 * Cette classe est responsable de la création d'un compte utilisateur,
 * en s'assurant que l'email est unique, et de la publication des événements
 * nécessaires pour la suite du processus d'enregistrement.
 */
#[AsCommandHandler]
final class RegisterUserHandler
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepositoryInterface $repository,
        private EventBusInterface $eventBus,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(RegisterUser $command): void
    {
        $dto = $command->dto;
        $email = new Email($dto->email);

        $spec = new IsUniqueSpecification($email);

        if (0 !== $this->repository->countBySpecification($spec)) {
            throw UserException::emailAlreadyExists($email);
        }

        $user = UserAccount::register($command->userId ?? UserId::generate(), $email);

        // encode the plain password
        $user->setHashPassword(
            new Password($this->userPasswordHasher->hashPassword($user, $dto->plainPassword))
        );

        $this->repository->save($user, true);

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
