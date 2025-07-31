<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Symfony\EventListener;

use Identity\Domain\Exception\InvalidIdentifierException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Listener global pour convertir les exceptions Identity en réponses HTTP appropriées.
 */
#[AsEventListener(event: KernelEvents::EXCEPTION, priority: 10)]
final readonly class IdentityExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof InvalidIdentifierException) {
            return;
        }

        // Réponse HTTP unifiée pour toutes les erreurs d'identifiant
        $response = new JsonResponse([
            'error' => 'invalid_identifier',
            'message' => $exception->getMessage(),
            'code' => 'IDENTIFIER_INVALID'
        ], 400);

        $event->setResponse($response);
    }
}
