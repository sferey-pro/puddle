<?php

declare(strict_types=1);

namespace App\Module\Sales\Infrastructure\Symfony;

use App\Module\Sales\Infrastructure\Symfony\DependencyInjection\SalesExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * La classe Bundle pour le module Sales.
 * Son rôle est d'intégrer le module dans l'écosystème Symfony,
 * notamment en exposant sa configuration de manière autonome.
 */
class SalesBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new SalesExtension();
        }

        return $this->extension ?: null;
    }
}
