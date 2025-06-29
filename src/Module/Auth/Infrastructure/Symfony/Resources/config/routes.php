<?php

declare(strict_types=1);

use App\Module\Auth\Domain\Enum\SocialNetwork;
use App\Module\Auth\UI\Controller\PasswordResetController;
use App\Module\Auth\UI\Controller\Security\OAuthCheckController;
use App\Module\Auth\UI\Controller\Security\OAuthConnectController;
use App\Module\Auth\UI\Controller\SecurityController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\EnumRequirement;

return static function (RoutingConfigurator $routes): void {
    $routes->add('register', '/register')
        ->controller([SecurityController::class, 'register'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;

    $routes->add('verify_email', '/verify/email')
        ->controller([SecurityController::class, 'verifyUserEmail'])
        ->methods([Request::METHOD_GET])
    ;

    $routes->add('login', '/login')
        ->controller([SecurityController::class, 'login'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;

    $routes->add('logout', '/logout')
        ->controller([SecurityController::class, 'logout'])
        ->methods([Request::METHOD_GET])
    ;

    $routes->add('login_link', '/login/link')
        ->controller([SecurityController::class, 'requestLoginLink'])
        ->methods([Request::METHOD_POST])
    ;

    $routes->add('login_link_sent', '/login/link/sent')
        ->controller([SecurityController::class, 'loginLinkSent'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;

    $routes->add('login_check', '/login/check')
        ->controller([SecurityController::class, 'check'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;

    $routes->add('security_oauth_check', '/oauth/check/{socialNetwork}')
        ->requirements([
            'socialNetwork' => new EnumRequirement(SocialNetwork::class),
        ])
        ->controller(OAuthCheckController::class)
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;

    $routes->add('security_oauth_connect', '/oauth/connect/{socialNetwork}')
        ->requirements([
            'socialNetwork' => new EnumRequirement(SocialNetwork::class),
        ])
        ->controller(OAuthConnectController::class)
        ->methods([Request::METHOD_GET])
    ;

    $resetPasswordRoutes = $routes->collection('forgot_password_')
        ->prefix('/reset-password');

    $resetPasswordRoutes->add('request', '/')
        ->controller([PasswordResetController::class, 'request']);

    $resetPasswordRoutes->add('check_email', '/check-email')
        ->controller([PasswordResetController::class, 'checkEmail']);

    $resetPasswordRoutes->add('reset_password', '/reset/{token}')
        ->controller([PasswordResetController::class, 'reset']);
};
