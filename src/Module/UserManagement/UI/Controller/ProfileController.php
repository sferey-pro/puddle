<?php

declare(strict_types=1);

namespace App\Module\UserManagement\UI\Controller;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Query\QueryBusInterface;
use App\Module\UserManagement\Application\Query\FindUserQuery;
use App\Module\UserManagement\Application\ReadModel\UserView;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Le ProfileController est responsable de la gestion du profil de l'utilisateur connecté.
 * Il permet à un utilisateur de consulter et de mettre à jour ses propres informations,
 * renforçant ainsi son autonomie et l'exactitude des données le concernant.
 */
#[IsGranted('ROLE_USER')]
final class ProfileController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    /**
     * Affiche le profil de l'utilisateur actuellement connecté.
     * Cette page agit comme une carte d'identité numérique au sein de l'application,
     * présentant les informations clés de l'utilisateur.
     */
    #[Template('@UserManagement/profile/show.html.twig')]
    public function show(): array
    {
        $userView = $this->findUserView();

        return [
            'user' => $userView,
        ];
    }

    /**
     * Permet à l'utilisateur de modifier ses propres informations de profil.
     * Cette fonctionnalité est essentielle pour que les données restent à jour,
     * par exemple en cas de changement de nom ou d'adresse.
     */
    #[Template('@UserManagement/profile/edit.html.twig')]
    public function edit(): array|RedirectResponse
    {
        $userView = $this->findUserView();

        return [
            'user' => $userView,
        ];
    }

    /**
     * Récupère le ReadModel (UserView) de l'utilisateur connecté.
     */
    private function findUserView(): UserView
    {
        /** @var \App\Module\Auth\Domain\UserAccount $user */
        $user = $this->getUser();

        return $this->queryBus->ask(new FindUserQuery($user->id()));
    }
}
