<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Contrôleur gérant la déconnexion de l'utilisateur.
 *
 * Ce contrôleur ne contient pas de logique métier explicite.
 * La déconnexion est interceptée et gérée par la configuration
 * du pare-feu de sécurité de Symfony.
 */
final class LogoutController extends AbstractController
{
    /**
     * Point d'entrée pour la déconnexion.
     *
     * Cette méthode est vide et lève une LogicException car la requête
     * est interceptée par le système de sécurité de Symfony avant son exécution.
     */
    public function __invoke(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
