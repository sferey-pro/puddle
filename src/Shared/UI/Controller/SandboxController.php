<?php

declare(strict_types=1);

namespace App\Shared\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

#[Route('/_sandbox', '_sandbox', env: 'dev')]
class SandboxController extends AbstractController
{
    #[Route('/', '_index')]
    public function index(RouterInterface $router): Response
    {
        $sandboxRoutes = [];
        // On récupère toutes les routes de l'application
        $allRoutes = $router->getRouteCollection()->all();

        foreach ($allRoutes as $name => $route) {
            $controllerAction = $route->getDefault('_controller');

            // On vérifie si la route pointe vers une méthode de ce contrôleur
            // et qu'il ne s'agit pas de la page d'index elle-même.
            if (\is_string($controllerAction)
                && str_starts_with($controllerAction, self::class)
                && '_sandbox_index' !== $name
            ) {
                // On extrait un nom lisible à partir du nom de la route
                $readableName = ucwords(mb_trim(str_replace(['sandbox_', '_'], ' ', $name)));
                $sandboxRoutes[] = [
                    'name' => $readableName,
                    'path' => $route->getPath(),
                ];
            }
        }

        return $this->render('@Shared/sandbox/index.html.twig', [
            'sandboxRoutes' => $sandboxRoutes,
        ]);
    }

    /**
     * Prévisualise l'e-mail de bienvenue envoyé aux nouveaux utilisateurs.
     *
     * Cette méthode simule l'envoi de l'e-mail, qui sera intercepté
     * par le profiler de Symfony en environnement de développement.
     */
    #[Route('/preview/welcome-email', '_preview_welcome_email', )]
    public function previewWelcomeEmail(Environment $twig): Response
    {
        $dummyToken = 'DUMMY_TOKEN_POUR_LE_PREVIEW_123456';

        $emailHtml = $twig->render('@Auth/emails/welcome_first_login.html.twig', [
            'action_url' => $dummyToken,
            'action_text' => 'Accéder à mon compte',
        ]);

        return new Response($emailHtml);
    }
}
