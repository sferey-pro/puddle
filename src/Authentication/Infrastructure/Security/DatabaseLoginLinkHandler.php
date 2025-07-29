<?php

namespace Authentication\Infrastructure\Security;

use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Domain\Model\AccessCredential\MagicLinkCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\ValueObject\Token\MagicLinkToken;
use InvalidArgumentException;
use Kernel\Application\Clock\SystemTime;
use LogicException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\LoginLink\Exception\InvalidLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandler;

/**
 * Décorateur qui ajoute la traçabilité au LoginLinkHandler de Symfony.
 */
#[AsDecorator(decorates: LoginLinkHandlerInterface::class)]
final class DatabaseLoginLinkHandler implements LoginLinkHandlerInterface
{
    public function __construct(
        #[AutowireDecorated]
        private readonly LoginLinkHandlerInterface $inner,
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        private readonly LoggerInterface $logger
    ) {}

    /**
     *
     * @param UserInterface|UserSecurity $user
     */
    public function createLoginLink(UserInterface $user, ?Request $request = null, ?int $lifetime = null): LoginLinkDetails
    {
        $latest = $this->credentialRepository->findLatestByIdentifier($user->getUserIdentifier());

        if ($latest && $latest->createdAt > new \DateTimeImmutable('-1 minute')) {
            throw new TooManyAttemptsException('Please wait before requesting a new link');
        }

        // 1. Symfony créer le lien sécurisé
        $loginLinkDetails = $this->inner->createLoginLink($user, $request, $lifetime ?? MagicLinkToken::EXPIRY_MINUTES);

        // 2. Extraire le token de l'URL
        $token = MagicLinkToken::create($this->extractTokenFromUrl($loginLinkDetails->getUrl()), $loginLinkDetails->getExpiresAt());

        if (!$user instanceof UserSecurity) {
            throw new \InvalidArgumentException('User must be instance of UserSecurity');
        }

        // Créer et sauvegarder le credential
        $credential = MagicLinkCredential::create(
            identifier: $user->identifier,
            token: $token,
            expiresAt: $loginLinkDetails->getExpiresAt(),
        );

        $this->credentialRepository->save($credential);

        return $loginLinkDetails;
    }

    public function consumeLoginLink(Request $request): UserInterface
    {
        $token = $this->extractTokenFromRequest($request);
        $expires = $this->extracExpiresFromRequest($request);

        $credential = $this->credentialRepository->findByToken(MagicLinkToken::create($token, new \DateTimeImmutable($expires)));

        if ($credential instanceof MagicLinkCredential) {

            if (!$credential->isValid()) {
                $this->logger->warning('Attempt to use already consumed magic link', [
                    'token' => substr($token, 0, 8) . '...',
                    'used_at' => $credential->usedAt?->format('Y-m-d H:i:s')
                ]);

                throw new InvalidLoginLinkException('This login link has already been used');
            }

            $user = $this->inner->consumeLoginLink($request);

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

    private function extractTokenFromRequest(Request $request): string
    {
        if (!$hash = $request->get('hash')) {
            throw new InvalidLoginLinkException('Missing "hash" parameter.');
        }
        if (!is_string($hash)) {
            throw new InvalidLoginLinkException('Invalid "hash" parameter.');
        }

        return $hash;
    }

    private function extracExpiresFromRequest(Request $request): string
    {
        if (!$expires = $request->get('expires')) {
            throw new InvalidLoginLinkException('Missing "expires" parameter.');
        }
        if (preg_match('/^\d+$/', $expires) !== 1) {
            throw new InvalidLoginLinkException('Invalid "expires" parameter.');
        }

        return $expires;
    }
}
