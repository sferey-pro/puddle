<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Adapter\In;

use Authentication\Domain\Service\CredentialServiceInterface;
use Shared\Domain\Service\IdentityContextInterface;
use Shared\Domain\ValueObject\EmailAddress;

final class IdentityAdapter implements CredentialServiceInterface
{
    public function __construct(
        private readonly IdentityContextInterface $identityContext
    ) {}

    public function findCredentialsByEmail(string $email): ?array
    {
        try {
            $emailVO = EmailAddress::fromString($email);
            $credentials = $this->identityContext->getIdentityCredentials($emailVO);

            if ($credentials === null) {
                return null;
            }

            return [
                'userId' => $credentials->userId->toString(),
                'hashedPassword' => $credentials->hashedPassword,
                'requires2FA' => $credentials->requires2FA(),
                'emailVerified' => $credentials->emailVerified
            ];
        } catch (\InvalidArgumentException) {
            return null;
        }
    }
}
