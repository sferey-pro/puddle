<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Adapter\Out;

use Identity\Domain\Model\ValueObject\Identifier;
use Identity\Domain\Repository\UserIdentityRepositoryInterface;
use SharedKernel\Domain\ValueObject\AccountId;
use SharedKernel\Domain\ValueObject\Email;
use SharedKernel\Domain\DTO\Identity\UserIdentifierDTO;
use SharedKernel\Domain\DTO\Identity\UserIdentifiersDTO;
use SharedKernel\Domain\Service\IdentityContextInterface;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class IdentityContextService implements IdentityContextInterface
{
    public function __construct(
        private readonly UserIdentityRepositoryInterface $userIdentityRepository
    ) {}

    public function getUserIdentifiers(UserId $userId): ?UserIdentifiersDTO
    {
        $userIdentity = $this->userIdentityRepository->findByUserId($userId);

        if ($userIdentity === null) {
            return null;
        }

        $identifierDTOs = [];
        foreach ($userIdentity->getIdentifiers() as $attachedIdentifier) {
            $identifierDTOs[] = new UserIdentifierDTO(
                type: $attachedIdentifier->identifier->getType(),
                value: $attachedIdentifier->identifier->getValue(),
                isPrimary: $attachedIdentifier->isPrimary,
                isVerified: $attachedIdentifier->isVerified,
                attachedAt: $attachedIdentifier->attachedAt
            );
        }

        return new UserIdentifiersDTO(
            accountId: $accountId,
            identifiers: $identifierDTOs,
            primaryIdentifier: $this->findPrimaryIdentifier($identifierDTOs)
        );
    }

    public function identifierExists(string $type, Identifier $value): bool
    {
        return $this->userIdentityRepository->existsByIdentifier($type, $value);
    }

    public function emailExists(EmailAddress $email): bool
    {
        return $this->identifierExists('email', $email);
    }

    private function findPrimaryIdentifier(array $identifiers): ?UserIdentifierDTO
    {
        foreach ($identifiers as $identifier) {
            if ($identifier->isPrimary) {
                return $identifier;
            }
        }
        return null;
    }
}
