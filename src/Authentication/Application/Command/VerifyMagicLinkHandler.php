<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Account\Core\Domain\Model\Account;
use Authentication\Domain\Event\AccountAuthenticatedViaPasswordless;
use Authentication\Domain\Exception\InvalidMagicLinkException;

#[AsCommandHandler]
final readonly class VerifyMagicLinkHandler
{
    public function __construct(
        private AccessCredentialRepositoryInterface $credentialRepository,
        private FluentAccountRepository $accountRepository,
        private SymfonyLoginLinkAdapter $loginLinkAdapter,
        private EventBusInterface $eventBus
    ) {}

    public function __invoke(VerifyMagicLink $command): Account
    {
        // 1. Trouver le credential
        $credential = $this->credentialRepository->findByMagicLinkToken($command->token);

        if (!$credential) {
            throw new InvalidMagicLinkException('Invalid or expired magic link');
        }

        // 2. Vérifier la validité
        if (!$credential->isValid() || $credential->isExpired(new \DateTime())) {
            throw new InvalidMagicLinkException('This magic link has expired');
        }

        // 3. Consommer avec Symfony
        $account = $this->loginLinkAdapter->consumeMagicLink($command->token);

        if (!$account) {
            throw new InvalidMagicLinkException('Unable to verify magic link');
        }

        // 4. Si compte pending, l'activer
        if ($account->isPending()) {
            $account->verify();
            $this->accountRepository->save($account);
        }

        // 5. Event
        $this->eventBus->publish(new AccountAuthenticatedViaPasswordless(
            userId: $account->getId(),
            method: 'magic_link',
            ipAddress: $command->ipAddress
        ));

        return $account;
    }
}
