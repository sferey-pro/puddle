<?php

declare(strict_types=1);

namespace App\Module\Sales\Infrastructure\ApiPlatform\State;

use App\Module\Sales\Application\Command\CreateOrder;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Action\NotFoundAction;
use App\Shared\Application\Command\CommandBusInterface;

class OrderProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        switch (true) {
            case $operation instanceof Post:
                return $this->commandBus->dispatch(new CreateOrder($data));
                break;

            default:
                throw new NotFoundAction();
                break;
        }
    }
}
