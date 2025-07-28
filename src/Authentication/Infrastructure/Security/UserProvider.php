<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security;

use Authentication\Application\Port\SecurityContextBuilder;
use SharedKernel\Domain\Service\AccountContextInterface;
use SharedKernel\Domain\Service\IdentityContextInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UserProvider implements UserProviderInterface
{
    public function __construct(
        private AccountContextInterface $accountContext,
        private IdentityContextInterface $identityContext,
        private SecurityContextBuilder $contextBuilder,
    ) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // 1. Essayer comme UserId d'abord (pour LoginLink)
        if ($this->looksLikeUserId($identifier)) {
            try {
                $userId = UserId::fromString($identifier);
                return $this->loadUserByUserId($userId);
            } catch (\Exception $e) {
                // Pas un UserId valide, continuer
            }
        }

        // 2. Sinon, chercher par email/phone
        $userId = $this->identityContext->findUserIdByIdentifier($identifier);

        if (!$userId) {
            throw new UserNotFoundException(sprintf('No user found for "%s"', $identifier));
        }

        return $this->loadUserByUserId($userId);
    }

    /**
     * Méthode interne pour charger par UserId.
     */
    private function loadUserByUserId(UserId $userId): UserInterface
    {
        $accountStatus = $this->accountContext->getAccountStatus($userId);

        if (!$accountStatus) {
            throw new UserNotFoundException('Account not found');
        }

        // Récupérer l'identifier principal pour UserSecurity
        $userIdentifiers = $this->identityContext->getUserIdentifiers($userId);
        $primaryIdentifier = $userIdentifiers?->primaryIdentifier?->value ?? 'unknown';

        return $this->contextBuilder->buildSecurityUser(
            $userId,
            $primaryIdentifier,
            $accountStatus
        );
    }

    private function looksLikeUserId(string $value): bool
    {
        // UUID format : 8-4-4-4-12
        return Uuid::isValid($value);
    }

    // public function loadUserByIdentifier(string $identifier): UserInterface
    // {
    //     // 1. Trouver l'UserId via Identity context
    //     $userId = $this->identityContext->findUserIdByIdentifier($identifier);

    //     if (!$userId) {
    //         throw new UserNotFoundException();
    //     }

    //     // 2. Vérifier le statut via Account context
    //     $accountStatus = $this->accountContext->getAccountStatus($userId);

    //     if (!$accountStatus || !$accountStatus->canAuthenticate()) {
    //         throw new DisabledException('Account is not active');
    //     }

    //     // 3. Construire l'objet Security
    //     return $this->contextBuilder->buildSecurityUser(
    //         $userId,
    //         $identifier,
    //         $accountStatus
    //     );
    // }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserSecurity) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByUserId($user->getUserId());
    }

    public function supportsClass(string $class): bool
    {
        return UserSecurity::class === $class;
    }
}
