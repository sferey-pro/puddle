<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Specification;

use App\Core\Specification\NotSpecification;
use App\Module\UserManagement\Domain\Enum\UserStatus;

/**
 * Vérifie si un utilisateur peut être désactivé.
 * Cette règle est la négation de "l'utilisateur a déjà le statut désactivé".
 */
final class UserCanBeDeactivatedSpecification extends NotSpecification
{
    public function __construct()
    {
        parent::__construct(new UserHasStatusSpecification(UserStatus::DEACTIVATED));
    }
}
