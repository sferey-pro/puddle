<?php

declare(strict_types=1);

namespace App\Module\Static\UI\Controller;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Contrôleur d'erreur minimaliste pour l'environnement de test.
 * Il retourne une réponse JSON propre au lieu d'une page HTML,
 * ce qui facilite grandement le débogage des tests fonctionnels.
 */
#[AsController]
final class ErrorTestController
{
    /**
     * Cette méthode est appelée par Symfony lorsqu'une exception se produit dans l'environnement de test.
     *
     * @param \Throwable $exception L'exception qui a été levée
     *
     * @return JsonResponse une réponse JSON simple avec les informations essentielles de l'erreur
     */
    public function __invoke(\Throwable $exception): JsonResponse
    {
        // On utilise FlattenException pour obtenir un statut de code HTTP propre, même pour les exceptions non-HTTP.
        $flatException = FlattenException::createFromThrowable($exception);
        $statusCode = $flatException->getStatusCode();

        $data = [
            'class' => $flatException->getClass(),
            'status_code' => $statusCode,
            'message' => $flatException->getMessage(),
            // On peut ajouter la trace si nécessaire pour un débogage plus poussé
            // 'trace' => $flatException->getTrace(),
        ];

        $response = new JsonResponse($data, $statusCode);

        $response->setEncodingOptions($response->getEncodingOptions() | \JSON_PRETTY_PRINT);

        return $response;
    }
}
