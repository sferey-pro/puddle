<?php

declare(strict_types=1);

use App\Module\User\UI\Controller\UserController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\Requirement;

return function (RoutingConfigurator $routes): void {

    $routes->add('user_index', '/users')
        ->controller([UserController::class, 'index'])
        ->defaults(['page' => '1', '_format' => 'html'])
        ->methods([Request::METHOD_GET])
    ;

    $routes->add('user_paginated', '/users/page/{page}')
        ->controller([UserController::class, 'index'])
        ->defaults(['_format' => 'html'])
        ->requirements(['page' => Requirement::POSITIVE_INT])
        ->methods([Request::METHOD_GET])
    ;

    $routes->add('user_new', '/user/new')
        ->controller([UserController::class, 'new'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;
};
