<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Adapter\Out;

use Identity\Domain\Repository\UserIdentityRepositoryInterface;
use Identity\Domain\Service\IdentifierResolverInterface;
use Kernel\Domain\Result;
use SharedKernel\Domain\DTO\Identity\UserIdentifierDTO;
use SharedKernel\Domain\DTO\Identity\UserIdentifiersDTO;
use SharedKernel\Domain\Service\IdentityContextInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class IdentityContextService implements IdentityContextInterface
{
    public function __construct(
        private readonly UserIdentityRepositoryInterface $userIdentityRepository,
        private readonly IdentifierResolverInterface $identifierResolver
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
            userId: $userId,
            identifiers: $identifierDTOs,
            primaryIdentifier: $this->findPrimaryIdentifier($identifierDTOs)
        );
    }

    public function findUserIdByIdentifier(string $identifierValue): ?UserId
    {
        return $this->userIdentityRepository->findUserIdByIdentifier($identifierValue);
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

    public function resolveIdentifier(string $value): ?Result
    {
        return $this->identifierResolver->resolve($value);
    }
}
