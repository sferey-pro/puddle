<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {

    $routes->import('../../src/Content/StaticContent/Controller', 'attribute');

    $routes->add('homepage', '/')
        ->controller([TemplateController::class, 'templateAction'])
        ->defaults(['template' => 'default/homepage.html.twig'])
    ;

    if ('dev' === $routes->env()) {
        $routes->import('@FrameworkBundle/Resources/config/routing/errors.php')
            ->prefix('/_error')
        ;
    }
};
