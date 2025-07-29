<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Domain\Model\AccessCredential\MagicLinkCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\ValueObject\Token\MagicLinkToken;
use Authentication\Infrastructure\Security\UserSecurity;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Authentication\Infrastructure\Security\UserProvider;

#[AsCommandHandler]
final class CreateMagicLinkHandler
{

    /**
     * @param UserProviderInterface|UserProvider $userProvider
     */
    public function __construct(
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        private readonly LoggerInterface $logger,
        private readonly RequestStack $requestStack,
        private readonly UserProviderInterface $userProvider
    ) {
    }

    public function __invoke(CreateMagicLink $command): void
    {
        $request = $this->requestStack->getMainRequest();
        $user = $this->userProvider->loadUserByUserId($command->userId);

        $latest = $this->credentialRepository->findLatestByIdentifier($user->getUserIdentifier());

        if ($latest && $latest->createdAt > new \DateTimeImmutable('-1 minute')) {
            throw new TooManyAttemptsException('Please wait before requesting a new link');
        }

        // 1. Symfony créer le lien sécurisé
        $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user, $request, $command->lifetime);

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
            identifier: $command->identifier,
            token: $token,
            expiresAt: $loginLinkDetails->getExpiresAt(),
        );

        $credential->attachToUser($command->userId);

        $this->credentialRepository->save($credential);
    }

    private function extractTokenFromUrl(string $url): string
    {
        $request = Request::create($url);
        return $request->get('hash');
    }
}
