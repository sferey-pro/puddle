<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Exception\UserException;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Specification\UniqueEmailSpecification;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
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
        private UniqueEmailSpecification $uniqueEmailSpecification,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(RegisterUser $command): void
    {
        $dto = $command->dto;
        $email = new Email($dto->email);

        // Vérifie l'unicité de l'email avant de créer le compte utilisateur,
        // garantissant que chaque utilisateur a une adresse email unique.
        if (!$this->uniqueEmailSpecification->isSatisfiedBy($email)) {
            throw UserException::notFoundWithEmail($email);
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
