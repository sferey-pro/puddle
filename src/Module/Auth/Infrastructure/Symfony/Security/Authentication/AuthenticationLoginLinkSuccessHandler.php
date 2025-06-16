<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security\Authentication;

use App\Module\Auth\Application\Command\VerifyLoginLink;
use App\Module\Auth\Domain\ValueObject\Hash;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;

class AuthenticationLoginLinkSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    protected array $defaultOptions = [
        'default_target_path' => 'admin',
    ];

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly HttpUtils $httpUtils,
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        $options = $this->defaultOptions;

        /** @var UserAccount $user */
        $user = $token->getUser();

        /** @var LoginLinkDetails $loginLinkDetails */
        $loginLinkDetails = $request->attributes->get('_login_link_details');

        // On déclenche la commande pour invalider le lien utilisé
        $this->commandBus->dispatch(new VerifyLoginLink(
            $user->id(),
            new Hash($request->get('hash')),
        ));

        return $this->httpUtils->createRedirectResponse($request, $options['default_target_path']);
    }
}
