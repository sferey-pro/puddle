<?php

declare(strict_types=1);

use App\Module\ProductCatalog\UI\Controller\ProductController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\Requirement;

return static function (RoutingConfigurator $routes): void {
    $productRoutes = $routes->collection('product_')
        ->prefix('/products'); // Préfixe commun pour les routes des produits

    // 1. Lister les produits (Read)
    $productRoutes->add('index', '/')
        ->controller([ProductController::class, 'index'])
        ->methods([Request::METHOD_GET]);

    // 2. Créer un nouveau produit (Create - afficher le formulaire & traiter la soumission)
    $productRoutes->add('new', '/new')
        ->controller([ProductController::class, 'new'])
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    // 3. Afficher un produit spécifique (Read - pour voir les détails ou avant de modifier)
    $productRoutes->add('show', '/{id}')
        ->controller([ProductController::class, 'show'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_GET]);

    // 4. Modifier un produit existant (Update - afficher le formulaire & traiter la soumission)
    $productRoutes->add('edit', '/{id}/edit')
        ->controller([ProductController::class, 'edit'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    // 5. Supprimer un produit (Delete)
    $productRoutes->add('delete', '/{id}/delete')
        ->controller([ProductController::class, 'delete'])
        ->requirements(['id' => Requirement::UUID_V7])
        ->methods([Request::METHOD_DELETE]);
};
