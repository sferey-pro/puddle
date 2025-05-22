<?php

declare(strict_types=1);

namespace App\Application\UserManagement\Service;

use App\Domain\UserManagement\Model\User;
use App\Infrastructure\UserManagement\ReadModel\View\UserView;

class UserViewMapper
{
    /**
     * Remplit un UserView existant ou nouveau avec les données d'un User du domaine.
     * L'ID du UserView doit être défini par l'appelant si c'est une création.
     */
    public function mapToView(User $user, UserView $userView): void
    {
    }
}
