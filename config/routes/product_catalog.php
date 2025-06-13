<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('@ProductCatalogBundle/Resources/config/routes.php')
        ->prefix('/admin');
};
