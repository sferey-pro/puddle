<?php

namespace Authentication\Application\Command;

use Identity\Domain\ValueObject\Identifier;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

final class InitiatePasswordlessLoginHandler
{
    public function __construct(
        private readonly AccessCredentialRepositoryInterface $repository,
        private readonly NotifierInterface $notifier,
        private readonly TokenGeneratorInterface $tokenGenerator
    ) {}

    public function __invoke(InitiatePasswordlessLogin $command): void
    {
        // 1. Vérifier que l'utilisateur existe
        $userId = $this->identityContext->resolveUserIdFromIdentifier(
            $command->identifier->value()
        );

        if (!$userId) {
            throw new UserNotFoundException();
        }

        // 2. Créer le credential (toujours le même type)
        $credential = PasswordlessCredential::create(
            identifier: $command->identifier,
            token: $this->tokenGenerator->generateSecureToken(),
            expiresAt: new \DateTimeImmutable('+15 minutes')
        );

        $this->repository->save($credential);

        // 3. Créer une notification unifiée
        $notification = new PasswordlessLoginNotification(
            token: $credential->token()->value(),
            expiresInMinutes: 15
        );

        // 4. Créer le recipient approprié selon l'identifier
        $recipient = $this->createRecipient($command->identifier);

        // 5. Le notifier choisit automatiquement SMS ou Email
        $this->notifier->send($notification, $recipient);
    }

    private function createRecipient(Identifier $identifier): RecipientInterface
    {
        return match($identifier->getType()) {
            'email' => new Recipient(
                email: $identifier->value()
            ),
            'phone' => new Recipient(
                phone: $identifier->value()
            ),
            default => throw new \InvalidArgumentException()
        };
    }
}
