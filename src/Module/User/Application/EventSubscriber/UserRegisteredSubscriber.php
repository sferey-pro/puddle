<?php

declare(strict_types=1);

namespace App\Module\User\Application\EventSubscriber;

use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\User\Application\Command\CreateUser;
use App\Module\User\Application\DTO\CreateUserDTO;
use App\Module\User\Application\Query\UserExistsQuery;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegisteredSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegistered::class => 'onUserRegistered',
        ];
    }

    public function onUserRegistered(UserRegistered $event): void
    {
        $userExists = $this->queryBus->ask(new UserExistsQuery(identifier: $event->identifier()));

        if (false === $userExists) {
            // Génération du username à partir de l'email
            $emailValue = $event->email()->value;
            $emailParts = explode('@', $emailValue);
            $usernameBase = $emailParts[0];

            // Nettoyage simple : minuscules, caractères alphanumériques, ., _, -
            $username = mb_strtolower($usernameBase);
            $username = preg_replace('/[^a-z0-9._-]/', '', $username);
            $username = mb_substr($username, 0, 30); // Limiter la longueur

            // Fallback si le username est vide après nettoyage
            if (empty($username)) {
                // Utilise une partie de l'identifiant unique de l'utilisateur
                $username = 'user_'.mb_substr(str_replace('-', '', (string) $event->identifier()), 0, 8);
            }

            $userCreate = new CreateUser(
                dto: new CreateUserDTO(
                    email: $event->email()->value,
                    username: $username
                ),
                identifier: $event->identifier()
            );

            $this->commandBus->dispatch($userCreate);
        }
    }
}
