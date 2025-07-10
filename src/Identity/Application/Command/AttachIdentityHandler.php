<?php

declare(strict_types=1);

namespace Identity\Application\Command;

use Identity\Domain\Exception\IdentityException;
use Identity\Domain\Repository\UserIdentityRepositoryInterface;
use Identity\Domain\Specification\IsUniqueIdentitySpecification;
use Identity\Domain\UserIdentity;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final class AttachIdentityHandler
{
    public function __construct(
        private readonly UserIdentityRepositoryInterface $repository,
        private readonly IsUniqueIdentitySpecification $uniqueSpec
    ) {
    }

    public function __invoke(AttachIdentity $command): void
    {
        // 1. Vérifier l'unicité
        if (!$this->uniqueSpec->isSatisfiedBy($command->identifier)) {
            throw IdentityException::identityAlreadyExists();
        }

        // 2. ✅ APPROCHE DDD CORRECTE : Récupérer l'agrégat existant
        $userIdentity = $this->repository->ofId($command->userId);

        if (null === $userIdentity) {
            // 3. Si pas d'agrégat existant, en créer un nouveau
            $userIdentity = UserIdentity::create($command->userId, $command->identifier);
        } else {
            // 4. Sinon, attacher la nouvelle identité à l'agrégat existant
            $userIdentity->attachIdentity($command->identifier);
        }

        // 5. Sauvegarder l'agrégat (les événements sont émis automatiquement)
        $this->repository->save($userIdentity);
    }
}
