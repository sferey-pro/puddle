<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\UserManagement\Domain\Exception\EmailAlreadyExistException;
use App\Module\UserManagement\Domain\Repository\CheckUserByEmailInterface;
use App\Module\UserManagement\Domain\Specification\UniqueEmailSpecificationInterface;
use Doctrine\ORM\NonUniqueResultException;

final class UniqueEmailSpecification extends AbstractSpecification implements UniqueEmailSpecificationInterface
{
    public function __construct(
        private readonly CheckUserByEmailInterface $checkUserByEmail,
    ) {
    }

    /**
     * @throws EmailAlreadyExistException
     */
    public function isUnique(Email $email): bool
    {
        return $this->isSatisfiedBy($email);
    }

    /**
     * @param Email $value
     */
    public function isSatisfiedBy($value): bool
    {
        try {
            if ($this->checkUserByEmail->existsEmail($value)) {
                throw new EmailAlreadyExistException();
            }
        } catch (NonUniqueResultException) {
            throw new EmailAlreadyExistException();
        }

        return true;
    }
}
