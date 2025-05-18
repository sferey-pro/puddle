<?php

declare(strict_types=1);

use App\Module\Auth\Domain\Enum\SocialNetwork;
use App\Module\Auth\UI\Controller\ForgotPasswordController;
use App\Module\Auth\UI\Controller\RegistrationController;
use App\Module\Auth\UI\Controller\ResetPasswordController;
use App\Module\Auth\UI\Controller\Security\LoginCheckController;
use App\Module\Auth\UI\Controller\Security\LoginController;
use App\Module\Auth\UI\Controller\Security\LoginLinkController;
use App\Module\Auth\UI\Controller\Security\LogoutController;
use App\Module\Auth\UI\Controller\Security\OAuth\CheckController;
use App\Module\Auth\UI\Controller\Security\OAuth\ConnectController;
use App\Module\Auth\UI\Controller\VerifyEmailController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\EnumRequirement;

return function (RoutingConfigurator $routes): void {
    $routes->add('register', '/register')
        ->controller(RegistrationController::class)
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;

    $routes->add('verify_email', '/verify/email')
        ->controller(VerifyEmailController::class)
        ->methods([Request::METHOD_GET])
    ;

    $routes->add('logout', '/logout')
        ->controller(LogoutController::class)
        ->methods([Request::METHOD_GET])
    ;

    $routes->add('login', '/login')
        ->controller(LoginController::class)
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;

    $routes->add('login_link', '/login/link')
        ->controller(LoginLinkController::class)
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;

    $routes->add('login_check', '/login/check')
        ->controller(LoginCheckController::class)
        ->methods([Request::METHOD_GET])
    ;

    $routes->add('security_oauth_check', '/oauth/check/{socialNetwork}')
        ->requirements([
            'socialNetwork' => new EnumRequirement(SocialNetwork::class),
        ])
        ->controller(CheckController::class)
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;

    $routes->add('security_oauth_connect', '/oauth/connect/{socialNetwork}')
        ->requirements([
            'socialNetwork' => new EnumRequirement(SocialNetwork::class),
        ])
        ->controller(ConnectController::class)
        ->methods([Request::METHOD_GET])
    ;

};
