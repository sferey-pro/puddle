<?php

use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {

    $routes->add('callback_spotify', '/callback/spotify')
        ->controller([TemplateController::class, 'templateAction'])
        ->defaults(['template' => 'default/homepage.html.twig'])
    ;

    $routes->import('@AuthBundle/Resources/config/routes.php')
        ->prefix('/')
    ;

    $routes->import('@UserManagementBundle/Resources/config/routes.php')
        ->prefix('/admin')
    ;

    $routes->import('@ProductCatalogBundle/Resources/config/routes.php')
        ->prefix('/admin')
    ;

    $routes->import('@CostManagementBundle/Resources/config/routes.php')
        ->prefix('/admin')
    ;

    $routes->import('@SalesBundle/Resources/config/routes.php')
        ->prefix('/admin/sales')
    ;
};
