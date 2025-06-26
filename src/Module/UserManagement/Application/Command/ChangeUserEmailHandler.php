<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\UserManagement\Domain\Exception\UserException;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;

#[AsCommandHandler]
final class ChangeUserEmailHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $repository,
    ) {
    }

    public function __invoke(ChangeUserEmail $command): void
    {
        $dto = $command->dto;

        // Récupère l'utilisateur à modifier. Si non trouvé, une exception métier est levée.
        $user = $this->repository->ofId($command->userId);
        if (null === $user) {
            throw UserException::notFoundWithId($command->userId);
        }

        $newEmail = new Email($dto->email);

        // Vérifie si la nouvelle adresse email est déjà utilisée par un autre utilisateur.
        // L'utilisateur actuel est exclu de la vérification, car il peut simplement confirmer son propre email.
        $spec = new IsUniqueSpecification(
            $newEmail,
            (string) $user->id() // On passe l'ID à exclure !
        );

        // On demande au repository de la vérifier.
        if (0 !== $this->repository->countBySpecification($spec)) {
            throw UserException::emailAlreadyExists($newEmail);
        }

        // Applique le changement d'email sur l'agrégat User, déclenchant un événement de domaine interne.
        $user->changeEmail($newEmail);
        $this->repository->save($user, true);

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
