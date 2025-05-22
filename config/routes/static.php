<?php

declare(strict_types=1);

use App\Module\Static\UI\Controller\HomepageController;
use App\Module\Static\UI\Controller\UnderConstructionController;
use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\HttpFoundation\Request;

return function (RoutingConfigurator $routes): void {

    $routes->add('homepage', '/')
        ->controller([TemplateController::class, 'templateAction'])
        ->defaults(['template' => 'default/homepage.html.twig'])
    ;

    $routes->add('profile', '/profile')
        ->controller(UnderConstructionController::class)
        ->methods([Request::METHOD_GET])
    ;

    $routes->add('settings', '/settings')
        ->controller(UnderConstructionController::class)
        ->methods([Request::METHOD_GET])
    ;
};

