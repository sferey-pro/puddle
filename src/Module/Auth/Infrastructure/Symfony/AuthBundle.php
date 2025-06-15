<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony;

use App\Module\Auth\Infrastructure\Symfony\DependencyInjection\AuthExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AuthBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new AuthExtension();
        }

        return $this->extension ?: null;
    }

}
