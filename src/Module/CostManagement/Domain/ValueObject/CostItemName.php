<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Core\Domain\ValueObject\AbstractStringValueObject;
use Assert\Assert;

/**
 * Représente le nom d'un poste de coût.
 * Ce Value Object garantit que le nom n'est pas vide et respecte une longueur maximale.
 */
final readonly class CostItemName extends AbstractStringValueObject
{
    /**
     * @return Result<self> Un Result contenant un CostItemName en cas de succès.
     */
    public static function create(string $email): Result
    {
        try {
            $normalizedEmail = strtolower($email);
            Assert::that($normalizedEmail)
                ->notEmpty('Cost item name cannot be empty.')
                ->maxLength(180, 'Cost item name cannot exceed 180 characters.');

            return Result::success(new self($normalizedEmail));
        } catch (\InvalidArgumentException $e) {
            return Result::failure(new \DomainException($e->getMessage()));
        }
    }
}
