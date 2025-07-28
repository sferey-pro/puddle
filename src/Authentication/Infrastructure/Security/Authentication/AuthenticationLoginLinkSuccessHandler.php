<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security\Authentication;

use Doctrine\ORM\EntityManagerInterface;
use Kernel\Application\Bus\CommandBusInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationLoginLinkSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    protected array $defaultOptions = [
        'default_target_path' => 'admin',
    ];

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly HttpUtils $httpUtils,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        $options = $this->defaultOptions;

        /** @var UserAccount $user */
        $user = $token->getUser();

        if (!$user->hasAlreadyLoggedIn()) {
            $user->recordFirstLogin();
            $this->em->flush();
        }

        return $this->httpUtils->createRedirectResponse($request, $options['default_target_path']);
    }
}
