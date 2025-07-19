<?php

declare(strict_types=1);

use Authentication\Infrastructure\Security\Authenticator\MagicLinkAuthenticator;
use Authentication\Infrastructure\Security\Authenticator\OTPAuthenticator;
use Authentication\Infrastructure\Security\UserProvider;
use Symfony\Config\SecurityConfig;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $containerConfigurator, SecurityConfig $security): void {

    $security->passwordHasher(PasswordAuthenticatedUserInterface::class)
        ->algorithm('auto')
    ;

    $security->provider('app_user_provider')
        ->id(UserProvider::class)
    ;

    $security->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false)
    ;

    $mainFirewall = $security->firewall('main');

    $mainFirewall
        ->lazy(true)
        ->provider('app_user_provider')
    ;

    $mainFirewall->loginLink()
        ->checkRoute('login_check')
        ->checkPostOnly(true)
        ->maxUses(1)
        ->signatureProperties(['userId'])
        ->lifetime(300)
    ;

    $mainFirewall->formLogin()
        ->loginPath('login')
        ->checkPath('login')
        ->enableCsrf(true)

    ;

    $mainFirewall->rememberMe()
        ->secret(param('kernel.secret'))
        ->lifetime(604800)
    ;

    $mainFirewall->customAuthenticators([
            MagicLinkAuthenticator::class,
            OTPAuthenticator::class,
        ])
        ->entryPoint('form_login')
    ;

    $mainFirewall->logout()
        ->path('logout')
        ->target('homepage')
    ;

    // $security->accessControl()
    //     ->path('^/admin')
    //     ->roles(['ROLE_USER']);

    // $security->accessControl()
    //     ->path('^/profile')
    //     ->roles(['ROLE_USER']);

    // $security->roleHierarchy('ROLE_ADMIN', ['ROLE_USER']);
    // $security->roleHierarchy('ROLE_SUPER_ADMIN', ['ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH']);

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 4,
                    'time_cost' => 3,
                    'memory_cost' => 10,
                ],
            ],
        ]);
    }
};
