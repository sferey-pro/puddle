<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\Username;
use Psr\Log\LoggerInterface;

/**
 * Handler pour la query FindUserByIdentifierQuery.
 * Il contient la logique pour déterminer si l'identifiant est un email ou un username
 * et interroger le repository correspondant.
 */
#[AsQueryHandler]
final readonly class FindUserByIdentifierQueryHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(FindUserByIdentifierQuery $query): ?UserAccount
    {
        try {
            $email = new Email($query->identifier);
            $user = $this->userRepository->ofEmail($email);
            if (null !== $user) {
                return $user;
            }
        } catch (\InvalidArgumentException) {
            // Ce n'était pas un email valide, on ignore et on passe à la suite.
        }

        // Si ce n'est pas un email, on essaie de le traiter comme un username.
        try {
            $username = new Username($query->identifier);

            return $this->userRepository->ofUsername($username);
        } catch (\InvalidArgumentException) {
            // Ce n'était pas un username valide.
        }

        $this->logger->emergency('Tentative de MagicLink non reconnue : '.$query->identifier);

        return null;
    }
}
