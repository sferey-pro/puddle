<?php

declare(strict_types=1);

namespace App\Shared\Application\EventSubscriber;

use App\Shared\Domain\Service\ClockInterface;
use App\Shared\Domain\Service\SystemTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Ce souscripteur initialise la façade statique SystemTime au début de chaque requête.
 * C'est la manière propre de configurer un état global pour la durée de la requête.
 */
final readonly class SystemTimeInitializerSubscriber implements EventSubscriberInterface
{
    public function __construct(private ClockInterface $clock)
    {
    }

    /**
     * Retourne les événements auxquels cette classe souscrit.
     */
    public static function getSubscribedEvents(): array
    {
        // On s'abonne à l'événement REQUEST du Kernel avec une haute priorité
        // pour s'assurer que notre code s'exécute très tôt.
        return [
            KernelEvents::REQUEST => ['initializeSystemTime', 255],
        ];
    }

    /**
     * La méthode exécutée lorsque l'événement KernelEvents::REQUEST est déclenché.
     */
    public function initializeSystemTime(): void
    {
        // On initialise notre façade statique avec le service Clock
        // injecté par le conteneur de services.
        SystemTime::set($this->clock);
    }
}
