<?php

declare(strict_types=1);

namespace App\Shared\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de développement pour afficher le catalogue de tous les composants d'interface (Styleguide / UI Kit).
 *
 * Il n'est accessible qu'en environnement de développement.
 */
final class StyleguideController extends AbstractController
{
    /**
     * Rend la page du styleguide.
     * La condition `kernel.debug` est la clé de sécurité qui garantit que
     * cette route et sa page n'existeront jamais en production.
     */
    #[Route(
        path: '/_styleguide',
        name: 'app_styleguide',
        condition: "service('kernel').isDebug()"
    )]
    public function __invoke(): Response
    {
        $buttonProps = ['label' => 'Click me', 'type' => 'button'];

        return $this->render('shared/ui/styleguide/index.html.twig', [
            'button_props' => $buttonProps,
        ]);
    }
}
