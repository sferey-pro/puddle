<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security;

use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Domain\Model\AccessCredential\MagicLinkCredential;
use Authentication\Domain\ValueObject\Token\MagicLinkToken;
use Kernel\Application\Clock\SystemTime;
use Psr\Log\LoggerInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\LoginLink\Exception\InvalidLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;

/**
 * Adapter pour intégrer Symfony LoginLink avec notre domaine
 */
final class LoginLinkAdapter
{
    public const LIFETIME = 300;

    /**
     * @param UserProviderInterface|UserProvider $userProvider
     * @return void
     */
    public function __construct(
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        private readonly LoggerInterface $logger,
        private readonly RequestStack $requestStack,
        private readonly UserProviderInterface $userProvider
    ) {}

    /**
     * Crée un Magic Link pour un compte
     */
    public function createLoginLink(UserId $userId, ?int $lifetime = null): LoginLinkDetails
    {
        $request = $this->requestStack->getMainRequest();

        $user = $this->userProvider->loadUserByUserId($userId);

        $latest = $this->credentialRepository->findLatestByIdentifier($user->identifier);

        if ($latest && $latest->createdAt > new \DateTimeImmutable('-1 minute')) {
            throw new TooManyAttemptsException('Please wait before requesting a new link');
        }

        // 1. Symfony créer le lien sécurisé
        $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user, $request, $lifetime ?? MagicLinkToken::EXPIRY_MINUTES);

        // 2. Extraire le token de l'URL
        $token = MagicLinkToken::fromString(
            $this->extractTokenFromUrl($loginLinkDetails->getUrl()),
            $loginLinkDetails->getExpiresAt()
        );

        if (!$user instanceof UserSecurity) {
            throw new \InvalidArgumentException('User must be instance of UserSecurity');
        }

        // Créer et sauvegarder le credential
        $credential = MagicLinkCredential::create(
            identifier: $user->identifier,
            token: $token,
            expiresAt: $loginLinkDetails->getExpiresAt(),
        );

        $credential->attachToUser($user->userId);

        $this->credentialRepository->save($credential);

        return $loginLinkDetails;
    }

    /**
     * Vérifie un Magic Link
     */
    public function consumeLoginLink(): UserInterface
    {
        $token = $this->extractToken();
        $expires = $this->extracExpires();

        $credential = $this->credentialRepository->findByToken(MagicLinkToken::fromString($token, new \DateTimeImmutable($expires)));

        if ($credential instanceof MagicLinkCredential) {

            if (!$credential->isValid()) {
                $this->logger->warning('Attempt to use already consumed magic link', [
                    'token' => substr($token, 0, 8) . '...',
                    'used_at' => $credential->usedAt?->format('Y-m-d H:i:s')
                ]);

                throw new InvalidLoginLinkException('This login link has already been used');
            }

            $user = $this->loginLinkHandler->consumeLoginLink($request);

            $credential->markAsUsed(SystemTime::now());
            $credential->addMetadata('consumed_ip', $request->getClientIp());

            $this->credentialRepository->save($credential);
        }

        return $user;
    }

    private function extractTokenFromUrl(string $url): string
    {
        $request = Request::create($url);
        return $request->get('hash');
    }

    private function extractToken(): string
    {
        $request = $this->requestStack->getMainRequest();

        if (!$hash = $request->get('hash')) {
            throw new InvalidLoginLinkException('Missing "hash" parameter.');
        }
        if (!is_string($hash)) {
            throw new InvalidLoginLinkException('Invalid "hash" parameter.');
        }

        return $hash;
    }

    private function extracExpires(): string
    {
        $request = $this->requestStack->getMainRequest();

        if (!$expires = $request->get('expires')) {
            throw new InvalidLoginLinkException('Missing "expires" parameter.');
        }
        if (preg_match('/^\d+$/', $expires) !== 1) {
            throw new InvalidLoginLinkException('Invalid "expires" parameter.');
        }

        return $expires;
    }
}
