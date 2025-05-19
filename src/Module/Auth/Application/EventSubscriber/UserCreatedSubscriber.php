<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\EventSubscriber;

use App\Module\Auth\Application\Command\Register\RegisterUser;
use App\Module\Auth\Application\DTO\RegisterUserDTO;
use App\Module\Auth\Application\Query\UserExistsQuery;
use App\Module\UserManagement\Domain\Event\UserCreated;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreated::class => 'onUserCreated',
        ];
    }

    public function onUserCreated(UserCreated $event): void
    {
        $userExists = $this->queryBus->ask(new UserExistsQuery(identifier: $event->identifier()));

        if (false === $userExists) {
            $userRegister = new RegisterUser(
                dto: new RegisterUserDTO(
                    email: $event->email()->value,
                    plainPassword: md5(random_bytes(10)),
                    agreeTerms: false
                ),
                identifier: $event->identifier()
            );

            $this->commandBus->dispatch($userRegister);
        }
    }
}
