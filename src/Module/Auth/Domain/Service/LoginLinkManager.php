<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Service;

use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\IpAddress;

/**
 * Service de domaine responsable de la gestion des liens de connexion magiques ("magic links").
 *
 * Son rôle est de coordonner le processus de création d'un lien de connexion à usage unique
 * pour un utilisateur. Il prend en charge une action qui ne trouve sa place naturelle
 * ni dans l'objet UserAccount (qui ne doit pas savoir comment générer les détails techniques
 * d'un lien), ni dans l'objet LoginLink lui-même.
 *
 * Ce service assure que la logique de création de lien reste propre et découplée.
 */
final readonly class LoginLinkManager
{
    public function __construct(
        private LoginLinkGeneratorInterface $loginLinkGenerator,
    ) {
    }

    /**
     * Crée un lien de connexion pour un utilisateur et l'associe à son compte.
     *
     * @param UserAccount $user      L'utilisateur pour qui le lien doit être créé
     * @param IpAddress   $ipAddress L'adresse IP depuis laquelle la demande a été faite (pour la sécurité)
     */
    public function createFor(UserAccount $user, IpAddress $ipAddress): void
    {
        // 1. Délègue la génération des détails uniques et sécurisés pour le lien.
        $loginLinkDetails = $this->loginLinkGenerator->generate($user);

        // 2. Demande à l'agrégat UserAccount d'associer ce nouveau lien.
        //    C'est la méthode `addLoginLink` au sein de UserAccount qui est responsable
        //    de valider la règle et de lever l'événement de domaine correspondant.
        $user->addLoginLink(
            $loginLinkDetails,
            $ipAddress
        );
    }
}
