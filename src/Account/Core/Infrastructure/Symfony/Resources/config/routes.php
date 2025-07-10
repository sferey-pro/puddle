<?php

declare(strict_types=1);

use Account\Registration\Presentation\Controller\RegistrationController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('register', '/register')
        ->controller([RegistrationController::class, 'register'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;
};
