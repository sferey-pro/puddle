<?php
namespace Authentication\Infrastructure\Security\Authenticator;

use Authentication\Application\Command\VerifyOTP;
use Authentication\Domain\Exception\InvalidOTPException;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Infrastructure\Security\UserProvider;
use Kernel\Application\Bus\CommandBusInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Gère l'authentification via code OTP (SMS).
 */
final class OTPAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly UserProvider $userProvider,
        private readonly LoggerInterface $logger,
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'passwordless_verify_otp'
            && $request->isMethod('POST')
            && $request->request->has('otp_code')
            && $request->request->has('phone');
    }

    public function authenticate(Request $request): Passport
    {
        $phoneNumber = $request->request->get('phone');
        $otpCode = $request->request->get('otp_code');

        if (!$phoneNumber || !$otpCode) {
            throw new CustomUserMessageAuthenticationException(
                'Phone number and verification code are required'
            );
        }

        $this->logger->info('Processing OTP authentication', [
            'phone_suffix' => '...' . substr($phoneNumber, -4)
        ]);

        try {
            // Vérifier l'OTP via la commande métier
            $command = new VerifyOTP(
                phoneNumber: $phoneNumber,
                code: $otpCode,
                ipAddress: $request->getClientIp(),
                userAgent: $request->headers->get('User-Agent')
            );

            $account = $this->commandBus->dispatch($command);

            return new SelfValidatingPassport(
                new UserBadge(
                    $account->getUserId()->toString(),
                    fn(string $uid) => $this->userProvider->loadUserByUserId(
                        UserId::fromString($uid)
                    )
                )
            );

        } catch (InvalidOTPException $e) {
            $this->logger->warning('Invalid OTP attempt', [
                'reason' => $e->getMessage(),
                'phone' => '...' . substr($phoneNumber, -4)
            ]);

            throw new CustomUserMessageAuthenticationException(
                'Invalid verification code. Please try again.'
            );

        } catch (TooManyAttemptsException $e) {
            $this->logger->warning('Too many OTP attempts', [
                'phone' => '...' . substr($phoneNumber, -4)
            ]);

            throw new TooManyLoginAttemptsAuthenticationException(
                ceil($e->getRetryAfter() / 60) // minutes
            );
        }
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        $this->logger->info('OTP authentication successful', [
            'user_id' => $token->getUserIdentifier()
        ]);

        // Laisser le controller gérer la redirection
        return null;
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response {
        // Stocker l'erreur pour l'afficher dans le formulaire
        $request->attributes->set('authentication_error', $exception);

        // Rester sur la même page pour réessayer
        return null;
    }
}
