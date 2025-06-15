<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\Symfony;

use App\Module\UserManagement\Infrastructure\Symfony\DependencyInjection\UserManagementExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserManagementBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new UserManagementExtension();
        }

        return $this->extension ?: null;
    }
}
