<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('@SalesBundle/Resources/config/routes.php')
        ->prefix('/admin/sales');
};
