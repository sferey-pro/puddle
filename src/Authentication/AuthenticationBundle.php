<?php

declare(strict_types=1);

namespace Authentication;

use Authentication\Infrastructure\Symfony\DependencyInjection\AuthenticationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AuthenticationBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new AuthenticationExtension();
        }

        return $this->extension ?: null;
    }
}
