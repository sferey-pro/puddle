<?php

declare(strict_types=1);

use App\Module\Static\UI\Controller\SandboxController;
use App\Module\Static\UI\Controller\UnderConstructionController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\HttpFoundation\Request;

return function (RoutingConfigurator $routes): void {

    $routes->add('sandbox_index', '/sandbox')
        ->controller([SandboxController::class, 'index'])
    ;

    $routes->add('sandbox_mercure', '/sandbox/publish')
        ->controller([SandboxController::class, 'publish'])
    ;
};

