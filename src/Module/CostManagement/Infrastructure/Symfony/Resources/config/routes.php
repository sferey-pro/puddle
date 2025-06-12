<?php

declare(strict_types=1);

use App\Module\CostManagement\UI\Controller\CostItemController;
use App\Module\CostManagement\UI\Controller\RecurringCostController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\Requirement;

return static function (RoutingConfigurator $routes): void {
    $costItemRoutes = $routes->collection('cost_item_')
        ->prefix('/cost-items/'); // Préfixe commun pour les routes d'administration des postes de couts

    // 1. Lister les produits (Read)
    $costItemRoutes->add('index', '/')
        ->controller([CostItemController::class, 'index'])
        ->methods([Request::METHOD_GET]);

    // 2. Créer un nouveau produit (Create - afficher le formulaire & traiter la soumission)
    $costItemRoutes->add('new', '/new')
        ->controller([CostItemController::class, 'new'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    // 3. Afficher un produit spécifique (Read - pour voir les détails ou avant de modifier)
    $costItemRoutes->add('show', '/{id}')
        ->controller([CostItemController::class, 'show'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_GET]);

    // 4. Modifier un produit existant (Update - afficher le formulaire & traiter la soumission)
    // $costItemRoutes->add('edit', '/{id}/edit')
    //     ->controller([CostItemController::class, 'edit'])
    //     ->requirements(['id' => Requirement::UUID_V7])
    //     ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    // 5. Supprimer un produit (Delete)
    // $costItemRoutes->add('delete', '/{id}/delete')
    //     ->controller([CostItemController::class, 'delete'])
    //     ->requirements(['id' => Requirement::UUID_V7])
    //     ->methods([Request::METHOD_DELETE]);

    // 6. Archivage d'un produit (Archive)
    $costItemRoutes->add('archive', '/{id}/archive')
        ->controller([CostItemController::class, 'archive'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_POST]);

    // 7. Réactivation d'un produit archivé (Reactivate)
    $costItemRoutes->add('reactivate', '/{id}/reactivate')
        ->controller([CostItemController::class, 'reactivate'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_POST]);

    $costRecurringRoutes = $routes->collection('recurring_cost_')
        ->prefix('/recurring-cost/');

    $costRecurringRoutes->add('index', '/')
        ->controller([RecurringCostController::class, 'index'])
        ->methods([Request::METHOD_GET]);

    $costRecurringRoutes->add('new', '/new/{from_item?}')
        ->controller([RecurringCostController::class, 'new'])
        ->methods([Request::METHOD_GET]);

    $costRecurringRoutes->add('show', '/{id}')
        ->controller([RecurringCostController::class, 'show'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_GET]);
};
