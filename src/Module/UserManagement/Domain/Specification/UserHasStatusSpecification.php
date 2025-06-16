<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\UserManagement\Domain\Enum\UserStatus;
use App\Module\UserManagement\Domain\User;

/**
 * Spécification qui vérifie si un User a un statut spécifique.
 *
 * @template-extends AbstractSpecification<User>
 */
class UserHasStatusSpecification extends AbstractSpecification
{
    public function __construct(private readonly UserStatus $expectedStatus)
    {
    }

    /**
     * @param User $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->status()->equals($this->expectedStatus);
    }
}
