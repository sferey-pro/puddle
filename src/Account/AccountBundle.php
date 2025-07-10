<?php

declare(strict_types=1);

namespace Account;

use Account\Core\Infrastructure\Symfony\DependencyInjection\AccountExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccountBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new AccountExtension();
        }

        return $this->extension ?: null;
    }
}
