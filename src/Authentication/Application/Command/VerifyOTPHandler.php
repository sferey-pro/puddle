<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Account\Core\Domain\Model\Account;
use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Authentication\Domain\Event\AccountAuthenticatedViaPasswordless;
use Authentication\Domain\Exception\InvalidMagicLinkException;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Infrastructure\Security\LoginLinkAdapter;
use Kernel\Application\Bus\EventBusInterface;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final readonly class VerifyOTPHandler
{
    public function __construct(
        private AccessCredentialRepositoryInterface $credentialRepository,
        private AccountRepositoryInterface $accountRepository,
        private LoginLinkAdapter $loginLinkAdapter,
        private EventBusInterface $eventBus
    ) {}

    public function __invoke(VerifyOTP $command): Account
    {
        // 1. Trouver le credential
        $credential = $this->credentialRepository->findByPhone($command->phoneNumber);

        if (!$credential) {
            throw new InvalidMagicLinkException('Invalid or expired OTP code');
        }

        // 2. Vérifier la validité
        if (!$credential->isValid() || $credential->isExpired(new \DateTime())) {
            throw new InvalidMagicLinkException('This OTP code has expired');
        }

        // 3. Consommer avec Symfony
        $account = $this->loginLinkAdapter->consumeMagicLink($command->code);

        if (!$account) {
            throw new InvalidMagicLinkException('Unable to verify OTP code');
        }

        // 4. Si compte pending, l'activer
        if ($account->isPending()) {
            $account->verify();
            $this->accountRepository->save($account);
        }

        // 5. Event
        $this->eventBus->publish(new AccountAuthenticatedViaPasswordless(
            userId: $account->getId(),
            method: 'otp',
            ipAddress: $command->ipAddress
        ));

        return $account;
    }
}
