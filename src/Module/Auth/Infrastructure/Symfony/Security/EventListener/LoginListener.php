<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security\EventListener;

use App\Core\Application\Event\EventBusInterface;
use App\Module\Auth\Domain\Event\UserLoggedIn;
use App\Module\Auth\Infrastructure\Symfony\Security\SecurityUser;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Écoute l'événement de succès de connexion de Symfony, quelle que soit
 * la méthode d'authentification, pour déclencher notre propre événement
 * d'application UserLoggedIn.
 */
#[AsEventListener(event: LoginSuccessEvent::class)]
final readonly class LoginListener
{
    public function __construct(private EventBusInterface $eventBus)
    {
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof SecurityUser) {
            $this->eventBus->publish(new UserLoggedIn($user->id));
        }
    }
}
