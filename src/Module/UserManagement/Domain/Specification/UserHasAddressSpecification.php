<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Specification;

use App\Core\Specification\SpecificationInterface;
use App\Module\UserManagement\Domain\User;

/**
 * Vérifie si un utilisateur a renseigné une adresse.
 */
final class UserHasAddressSpecification implements SpecificationInterface
{
    /**
     * @param User $object
     */
    public function isSatisfiedBy(object $object): bool
    {
        return null !== $object->address() && '' !== $object->address();
    }
}
