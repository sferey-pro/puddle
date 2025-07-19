<?php

declare(strict_types=1);

namespace Kernel;

use Kernel\Infrastructure\Symfony\DependencyInjection\KernelExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KernelBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new KernelExtension();
        }

        return $this->extension ?: null;
    }
}
