<?php

namespace Identity\Application\Command;

use Identity\Domain\Repository\UserIdentityRepositoryInterface;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Psr\Log\LoggerInterface;

#[AsCommandHandler]
final readonly class CompensateIdentityAttachmentHandler
{
    public function __construct(
        private UserIdentityRepositoryInterface $repository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(CompensateIdentityAttachment $command): void
    {
        $userIdentity = $this->repository->ofId($command->userId);

        if (null === $userIdentity) {
            // Pas d'agrégat à compenser, c'est OK
            $this->logger->info('No UserIdentity found for compensation', [
                'user_id' => (string) $command->userId,
                'identifier' => $command->identifier->value()
            ]);
            return;
        }

        // Détacher l'identité de l'agrégat
        $userIdentity->detachIdentity($command->identifier);

        // Si c'était la seule identité, supprimer complètement l'agrégat
        if ($userIdentity->hasNoIdentities()) {
            $this->repository->remove($userIdentity);
        } else {
            $this->repository->save($userIdentity);
        }
    }
}
