<?php

declare(strict_types=1);

namespace SharedKernel;

use SharedKernel\Infrastructure\Symfony\DependencyInjection\SharedKernelExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SharedKernelBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new SharedKernelExtension();
        }

        return $this->extension ?: null;
    }
}
