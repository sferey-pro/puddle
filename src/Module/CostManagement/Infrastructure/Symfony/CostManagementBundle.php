<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Symfony;

use App\Module\CostManagement\Infrastructure\Symfony\DependencyInjection\CostManagementExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CostManagementBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new CostManagementExtension();
        }

        return $this->extension ?: null;
    }
}
