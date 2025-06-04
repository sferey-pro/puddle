<?php

declare(strict_types=1);

use App\Module\ProductCatalog\UI\Controller\AdminProductController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\Requirement;

return static function (RoutingConfigurator $routes): void {
    $adminProductRoutes = $routes->collection('admin_product_')
        ->prefix('/admin/products'); // Préfixe commun pour les routes d'administration des produits

    // 1. Lister les produits (Read)
    $adminProductRoutes->add('index', '/')
        ->controller([AdminProductController::class, 'index'])
        ->methods([Request::METHOD_GET]);

    // 2. Créer un nouveau produit (Create - afficher le formulaire & traiter la soumission)
    $adminProductRoutes->add('new', '/new')
        ->controller([AdminProductController::class, 'new'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    // 3. Afficher un produit spécifique (Read - pour voir les détails ou avant de modifier)
    $adminProductRoutes->add('show', '/{id}')
        ->controller([AdminProductController::class, 'show'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_GET]);

    // 4. Modifier un produit existant (Update - afficher le formulaire & traiter la soumission)
    $adminProductRoutes->add('edit', '/{id}/edit')
        ->controller([AdminProductController::class, 'edit'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    // 5. Supprimer un produit (Delete)
    $adminProductRoutes->add('delete', '/{id}/delete')
        ->controller([AdminProductController::class, 'delete'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_DELETE]);

};
