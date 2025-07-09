<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
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
    ) {
    }

    public function __invoke(FindUserByIdentifierQuery $query): ?UserAccount
    {
        $emailResult = EmailAddress::create($query->identifier);

        if ($emailResult->isSuccess()) {
            return $this->userRepository->ofEmail($emailResult->value());
        }

        return null;
    }
}
