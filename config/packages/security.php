<?php

declare(strict_types=1);

use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Infrastructure\Symfony\Security\Authentication\AuthenticationLoginLinkFailureHandler;
use App\Module\Auth\Infrastructure\Symfony\Security\Authentication\AuthenticationLoginLinkSuccessHandler;
use App\Module\Auth\Infrastructure\Symfony\Security\GoogleAuthenticator;
use Symfony\Config\SecurityConfig;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $containerConfigurator, SecurityConfig $security): void {

    $security->passwordHasher(PasswordAuthenticatedUserInterface::class)
        ->algorithm('auto')
    ;

    $security->provider('app_user_provider')
        ->entity()
            ->class(UserAccount::class)
            ->property('email.value')
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
        ->signatureProperties(['id'])
        ->lifetime(300)
        ->successHandler(AuthenticationLoginLinkSuccessHandler::class)
        ->failureHandler(AuthenticationLoginLinkFailureHandler::class)
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

    $mainFirewall->customAuthenticators([GoogleAuthenticator::class]);

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
