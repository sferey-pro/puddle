<?php

declare(strict_types=1);

use App\Module\UserManagement\UI\Controller\UserController;
use App\Module\UserManagement\UI\Controller\UserViewController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\Requirement;

return function (RoutingConfigurator $routes): void {

    $routes->add('user_index', '/users')
        ->controller([UserViewController::class, 'index'])
        ->defaults(['_format' => 'html'])
        ->methods([Request::METHOD_GET])
    ;

    $routes->add('user_new', '/user/new')
        ->controller([UserController::class, 'new'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;
};
