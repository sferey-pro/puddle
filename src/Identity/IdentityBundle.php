<?php

declare(strict_types=1);

namespace Identity;

use Identity\Infrastructure\Symfony\DependencyInjection\IdentityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IdentityBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new IdentityExtension();
        }

        return $this->extension ?: null;
    }
}
