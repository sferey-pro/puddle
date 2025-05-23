<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {

    $routes->add('callback_spotify', '/callback/spotify')
        ->controller([TemplateController::class, 'templateAction'])
        ->defaults(['template' => 'default/homepage.html.twig'])
    ;
};

