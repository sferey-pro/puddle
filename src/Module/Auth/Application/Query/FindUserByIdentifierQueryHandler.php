<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;
use App\Module\Auth\Domain\Exception\UserException;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Service\IdentifierResolver;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\EmailIdentity;
use App\Module\Auth\Domain\ValueObject\PhoneIdentity;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;

/**
 * Handler pour la query FindUserByIdentifierQuery.
 * Il contient la logique pour déterminer si l'identifiant est un email
 * et interroger le repository correspondant.
 */
#[AsQueryHandler]
final readonly class FindUserByIdentifierQueryHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private IdentifierResolver $identifierResolver,
    ) {
    }

    public function __invoke(FindUserByIdentifierQuery $query): ?UserAccount
    {
        $identityResult = $this->identifierResolver->resolve($query->identifier);

        if ($identityResult->isFailure()) {
            throw new \InvalidArgumentException($identityResult->error()->getMessage());
        }

        /** @var UserIdentity $identity */
        $identity = $identityResult->value();

        $userAccount = match ($identity::class) {
            EmailIdentity::class => $this->userRepository->ofEmail($identity->value()),
            PhoneIdentity::class => $this->userRepository->ofPhone($identity->value()),
            default => throw new \InvalidArgumentException(
                sprintf('Unsupported identity type: %s', $identity::class)
            ),
        };

        if (null === $userAccount) {
            return null;
        }

        return $userAccount;
    }
}
