<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Domain\Model\AccessCredential\MagicLinkCredential;
use Authentication\Domain\Model\AccessCredential\OTPCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\Service\TokenGeneratorInterface;
use Authentication\Domain\ValueObject\Token\MagicLinkToken;
use Authentication\Domain\ValueObject\Token\OTPCode;
use Authentication\Infrastructure\Security\UserSecurity;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Authentication\Infrastructure\Security\UserProvider;

#[AsCommandHandler]
final class CreateOTPHandler
{

    /**
     * @param UserProviderInterface|UserProvider $userProvider
     */
    public function __construct(
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateMagicLink $command): void
    {
        $userId = $command->userId;
        $identifier = $command->identifier;

        // Générer un code OTP pour SMS
        $otpCode = OTPCode::generate();

        // Créer et sauvegarder le credential
        $credential = OTPCredential::create(
            identifier: $identifier,
            token: $otpCode,
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
