<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security\Authenticator;

use Authentication\Domain\Event\UserAuthenticatedViaMagicLink;
use Authentication\Infrastructure\Security\TrackedLoginLinkHandler;
use Kernel\Application\Bus\EventBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\LoginLink\Exception\ExpiredLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\Exception\InvalidLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Authenticator pour magic links avec traçabilité.
 */
final class MagicLinkAuthenticator extends AbstractAuthenticator
    implements AuthenticationEntryPointInterface
{
    use TargetPathTrait;

    public function __construct(
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly EventBusInterface $eventBus,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Vérifie si cette requête doit être gérée par cet authenticator.
     */
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'auth_magic_link_verify'
            && ($request->attributes->has('token') || $request->query->has('token'));
    }

    /**
     * Crée un passport pour l'authentification.
     */
    public function authenticate(Request $request): Passport
    {
        try {
            // Utiliser notre TrackedLoginLinkHandler qui :
            // 1. Vérifie dans la DB si le lien est déjà utilisé
            // 2. Délègue à Symfony pour la validation cryptographique
            // 3. Marque le lien comme utilisé
            $user = $this->loginLinkHandler->consumeLoginLink($request);

            $this->logger->info('Magic link consumed successfully', [
                'user' => $user->getUserIdentifier()
            ]);

            // Créer un passport avec l'utilisateur authentifié
            return new SelfValidatingPassport(
                new UserBadge(
                    $user->getUserIdentifier(),
                    fn() => $user  // L'utilisateur est déjà chargé
                )
            );

        } catch (ExpiredLoginLinkException $e) {
            $this->logger->warning('Expired magic link attempt', [
                'ip' => $request->getClientIp()
            ]);

            throw new CustomUserMessageAuthenticationException(
                'This login link has expired. Please request a new one.'
            );

        } catch (InvalidLoginLinkException $e) {
            $this->logger->warning('Invalid magic link attempt', [
                'message' => $e->getMessage(),
                'ip' => $request->getClientIp()
            ]);

            // Message personnalisé selon l'erreur
            $message = str_contains($e->getMessage(), 'already been used')
                ? 'This login link has already been used. Please request a new one.'
                : 'This login link is invalid. Please request a new one.';

            throw new CustomUserMessageAuthenticationException($message);

        } catch (\Exception $e) {
            $this->logger->error('Magic link authentication error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new CustomUserMessageAuthenticationException(
                'An error occurred during authentication. Please try again.'
            );
        }
    }

    /**
     * Appelé après une authentification réussie.
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        $user = $token->getUser();

        // Event pour l'audit
        $this->eventBus->publish(new UserAuthenticatedViaMagicLink(
            userId: $user->getUserId(),
            ipAddress: $request->getClientIp(),
            userAgent: $request->headers->get('User-Agent', 'Unknown'),
            authenticatedAt: new \DateTimeImmutable()
        ));

        // Message de succès
        $request->getSession()->getFlashBag()->add(
            'success',
            'Welcome! You have been successfully logged in.'
        );

        // Redirection vers la cible sauvegardée ou dashboard
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);

        return new RedirectResponse(
            $targetPath ?: $this->urlGenerator->generate('dashboard')
        );
    }

    /**
     * Appelé en cas d'échec de l'authentification.
     */
    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response {
        // Log l'échec
        $this->logger->warning('Magic link authentication failed', [
            'reason' => $exception->getMessage(),
            'ip' => $request->getClientIp()
        ]);

        // Message d'erreur
        $errorMessage = $exception instanceof CustomUserMessageAuthenticationException
            ? $exception->getMessage()
            : 'Authentication failed. Please request a new login link.';

        $request->getSession()->getFlashBag()->add('error', $errorMessage);

        return new RedirectResponse(
            $this->urlGenerator->generate('passwordless_request')
        );
    }

    /**
     * Point d'entrée quand un utilisateur non authentifié accède à une ressource protégée.
     */
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        // Sauvegarder l'URL cible pour redirection après login
        $this->saveTargetPath($request->getSession(), 'main', $request->getUri());

        $request->getSession()->getFlashBag()->add(
            'info',
            'Please log in to access this page.'
        );

        return new RedirectResponse(
            $this->urlGenerator->generate('passwordless_request')
        );
    }
}
