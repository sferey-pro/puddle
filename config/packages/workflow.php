<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/**
 * Fichier principal pour la configuration des workflows.
 * Son seul rôle est d'importer les configurations spécifiques
 * de chaque workflow de l'application pour les rendre visibles par Symfony.
 */
return static function (ContainerConfigurator $container): void {
    // On importe la configuration pour notre saga d'inscription.
    $container->import(dirname(__DIR__, 1) . '/workflows/registration_saga.php');
};
