<?php

namespace Symfony\Component\Routing\Loader\Configurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('%kernel.project_dir%/src/Module/CostManagement/Infrastructure/Resources/config/routes.php')
        ->prefix('/admin')
    ;
};
