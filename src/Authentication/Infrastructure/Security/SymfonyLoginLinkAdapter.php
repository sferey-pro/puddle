<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security;

use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Authentication\Domain\Service\TokenGeneratorInterface;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Account\Core\Domain\Model\Account;
use Authentication\Domain\Model\AccessCredential\MagicLinkCredential;
use Authentication\Domain\ValueObject\Token\MagicLinkToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;

/**
 * Adapter pour intégrer Symfony LoginLink avec notre domaine
 */
final class SymfonyLoginLinkAdapter
{
    public const LIFETIME = 300;

    public function __construct(
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        private readonly TokenGeneratorInterface $tokenGenerator
    ) {}

    /**
     * Crée un Magic Link pour un compte
     */
    public function createMagicLinkForAccount(Account $account): LoginLinkDetails
    {
        // 1. Utiliser Symfony pour générer le lien
        $loginLinkDetails = $this->loginLinkHandler->createLoginLink($account, null, self::LIFETIME);

        // 2. Extraction des informations depuis l'URL générée par Symfony.
        $request = Request::create($loginLinkDetails->getUrl());
        $token = $request->get('hash');

        // 3. Créer notre credential domain object
        $credential = MagicLinkCredential::create(
            identifier: $account->identifier,
            token: MagicLinkToken::fromString($token),
            expiresAt: $loginLinkDetails->getExpiresAt()
        );

        $credential->attachToUser($account->id);

        // 4. Persister
        $this->credentialRepository->save($credential);

        return $loginLinkDetails;
    }

    /**
     * Vérifie un Magic Link
     */
    public function consumeMagicLink(string $token): ?Account
    {
        // Utiliser Symfony pour consommer le lien
        $user = $this->loginLinkHandler->consumeLoginLink($this->request);

        if ($user instanceof Account) {
            // Marquer notre credential comme utilisé
            $credential = $this->credentialRepository->findByToken(
                MagicLinkToken::fromString($token)
            );

            if ($credential) {
                $credential->markAsUsed(new \DateTimeImmutable());
                $this->credentialRepository->save($credential);
            }

            return $user;
        }

        return null;
    }
}
