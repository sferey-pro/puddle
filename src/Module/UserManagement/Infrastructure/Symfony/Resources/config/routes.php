<?php

declare(strict_types=1);

use App\Module\UserManagement\UI\Controller\UserController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $userRoutes = $routes->collection('user_')
        ->prefix('/users'); // Préfixe commun pour les routes des produits

    $userRoutes->add('index', '/')
        ->controller([UserController::class, 'index'])
        ->defaults(['_format' => 'html'])
        ->methods([Request::METHOD_GET])
    ;

    $userRoutes->add('new', '/new')
        ->controller([UserController::class, 'new'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST])
    ;
};
