<?php

declare(strict_types=1);

namespace Identity\Application\Command;

use Identity\Domain\Exception\IdentityException;
use Identity\Domain\Repository\UserIdentityRepositoryInterface;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final readonly class DetachIdentityFromAccountHandler
{
    public function __construct(
        private UserIdentityRepositoryInterface $repository
    ) {
    }

    public function __invoke(DetachIdentityFromAccount $command): void
    {
        $userIdentity = $this->repository->ofId($command->userId);

        if (null === $userIdentity) {
            throw IdentityException::userIdentityNotFound($command->userId);
        }

        // Sécurité : vérifier si on peut supprimer cette identité
        if ($userIdentity->isPrimaryIdentity($command->identifier) && !$command->forceRemovePrimary) {
            throw IdentityException::cannotRemovePrimaryIdentity();
        }

        $userIdentity->detachIdentity($command->identifier);

        // Si plus d'identités, supprimer l'agrégat complet
        if ($userIdentity->hasNoIdentities()) {
            $this->repository->remove($userIdentity);
        } else {
            $this->repository->save($userIdentity);
        }
    }
}
