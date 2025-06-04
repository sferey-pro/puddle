<?php

namespace Symfony\Component\Routing\Loader\Configurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('@CostManagementBundle/Resources/config/routes.php')
        ->prefix('/admin')
    ;
};
