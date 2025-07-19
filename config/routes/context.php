<?php

use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {

    // $routes->add('callback_spotify', '/callback/spotify')
    //     ->controller([TemplateController::class, 'templateAction'])
    //     ->defaults(['template' => 'default/homepage.html.twig'])
    // ;

    $routes->import('@AccountBundle/Resources/config/routes.php')
        ->prefix('/')
    ;

};
