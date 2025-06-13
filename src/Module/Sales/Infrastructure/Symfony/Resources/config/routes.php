<?php

use App\Module\Sales\UI\Controller\OrderController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\Requirement;

return static function (RoutingConfigurator $routes): void {
    $orderRoutes = $routes->collection('sales_order_')
        ->prefix('/orders/'); // Préfixe commun pour les routes d'administration des orders

    // 1. Lister les commandes (Read)
    $orderRoutes->add('index', '/')
        ->controller([OrderController::class, 'index'])
        ->methods([Request::METHOD_GET]);

    // 2. Créer une nouvelle commandes (Create - afficher le formulaire & traiter la soumission)
    $orderRoutes->add('new', '/new')
        ->controller([OrderController::class, 'new'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    // 3. Afficher une commandes spécifique (Read - pour voir les détails ou avant de modifier)
    $orderRoutes->add('show', '/{id}')
        ->controller([OrderController::class, 'show'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_GET]);
};
