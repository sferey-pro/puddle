<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Service;

use App\Module\Auth\Domain\LoginLink;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\IpAddress;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;

/**
 * Service de domaine pur responsable de la gestion des liens de connexion.
 *
 * Son rôle est d'orchestrer la création et l'association des liens de connexion
 * en s'assurant du respect des règles métier. Il ne contient aucun effet de bord
 * (pas d'envoi d'événement, pas de persistance directe).
 */
final readonly class LoginLinkManager
{
    public function __construct(
        private LoginLinkGeneratorInterface $loginLinkGenerator,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Crée un lien de connexion pour un utilisateur existant qui en fait la demande.
     *
     * @return UserAccount L'agrégat UserAccount mis à jour, contenant l'événement de domaine
     */
    public function createForStandardLogin(UserAccount $user, IpAddress $ipAddress): UserAccount
    {
        $loginLinkDetails = $this->loginLinkGenerator->generate($user);

        // L'entité UserAccount est responsable de créer et d'enregistrer l'événement.
        $user->addLoginLink($loginLinkDetails, $ipAddress);

        return $user;
    }

    /**
     * Crée le premier lien de connexion pour un nouvel utilisateur à la fin de la Saga.
     *
     * @return LoginLink L'entité LoginLink créée, avec son token en clair accessible
     */
    public function createForNewUser(EmailAddress $email): LoginLink
    {
        $userAccount = $this->userRepository->ofEmail($email);
        if (!$userAccount) {
            throw new \LogicException('User account must exist at this stage of the Saga.');
        }

        $loginLinkDetails = $this->loginLinkGenerator->generate($userAccount);

        $loginLink = LoginLink::createFor($userAccount, $loginLinkDetails);

        return $loginLink;
    }
}
