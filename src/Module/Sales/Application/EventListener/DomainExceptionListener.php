<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\EventListener;

use App\Module\Sales\Domain\Exception\OrderException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class DomainExceptionListener
{

    public function __invoke(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        $exception = $event->getThrowable();
        if (!$this->isApiRequest($request)) {
            return;
        }


        if ($exception instanceof OrderException) {
            $response = match ($exception->errorCode()) {
                OrderException::NOT_FOUND => $this->createErrorResponse($exception, Response::HTTP_NOT_FOUND),
                OrderException::PRODUCT_NOT_FOUND => $this->createErrorResponse($exception, Response::HTTP_UNPROCESSABLE_ENTITY),
                default => $this->createErrorResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR),
            };
        }
    }

    private function isApiRequest(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api/');
    }

    /**
     * Méthode utilitaire pour construire une réponse d'erreur JSON standardisée.
     */
    private function createErrorResponse(OrderException $exception, int $statusCode): JsonResponse
    {
        $data = [
            'status' => $statusCode,
            'detail' => $exception->getMessage(),
        ];

        // Si l'exception a un errorCode, on l'ajoute.
        if (method_exists($exception, 'errorCode')) {
            $data['error_code'] = $exception->errorCode();
        }

        return new JsonResponse($data, $statusCode);
    }

}
