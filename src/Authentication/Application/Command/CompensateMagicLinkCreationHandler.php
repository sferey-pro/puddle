<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Psr\Log\LoggerInterface;

#[AsCommandHandler]
final class CompensateMagicLinkCreationHandler
{

    public function __construct(
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(CompensateMagicLinkCreation $command): void
    {
        $credential = $this->credentialRepository->findByIdentifierAndUserId($command->identifier, $command->userId);

        if (null !== $credential) {
            $this->credentialRepository->remove($credential);

            $this->logger->info('Passwordless credential compensation completed', [
                'user_id' => (string) $command->userId
            ]);
        }
    }

}
