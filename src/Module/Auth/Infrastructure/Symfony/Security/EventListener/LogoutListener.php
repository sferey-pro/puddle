<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security\EventListener;

use App\Core\Application\Event\EventBusInterface;
use App\Module\Auth\Domain\Event\UserLoggedOut;
use App\Module\Auth\Infrastructure\Symfony\Security\SecurityUser;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * Écoute l'événement de déconnexion de Symfony pour déclencher notre
 * propre événement d'application UserLoggedOut.
 */
#[AsEventListener(event: LogoutEvent::class)]
final readonly class LogoutListener
{
    public function __construct(private EventBusInterface $eventBus)
    {
    }

    public function __invoke(LogoutEvent $event): void
    {
        $token = $event->getToken();

        if ($token && ($user = $token->getUser()) instanceof SecurityUser) {
            $this->eventBus->publish(new UserLoggedOut($user->id));
        }
    }
}
