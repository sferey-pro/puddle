<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security;

use App\Module\Auth\Domain\Enum\SocialNetwork;
use App\Module\Auth\Domain\Model\User;
use App\Module\Auth\Infrastructure\Symfony\Services\OAuth\OAuthRegistration;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

abstract class AbstractOAuth2Authenticator extends OAuth2Authenticator
{
    use TargetPathTrait;

    protected SocialNetwork $serviceName;

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly RouterInterface $router,
        private readonly OAuthRegistration $registrationService,
        protected readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return 'security_oauth_check' === $request->attributes->get('_route')
            && $this->serviceName === SocialNetwork::tryFrom($request->get('socialNetwork'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);

        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('homepage'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('login'));
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $accessToken = $this->fetchAccessToken($this->getClient());
        $resourceOwner = $this->getResourceOwnerFromCredentials($accessToken);

        $user = $this->getUserFromResourceOwner($resourceOwner);

        if (null === $user) {
            $user = $this->registrationService->persist($this->serviceName, $resourceOwner);
        }

        return new SelfValidatingPassport(
            userBadge: new UserBadge($user->getUserIdentifier(), fn () => $user),
            badges: [
                new RememberMeBadge(),
            ]
        );
    }

    protected function getResourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface
    {
        return $this->getClient()->fetchUserFromToken($credentials);
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient($this->serviceName->value);
    }

    abstract protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner): ?User;
}
