<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Validator\Constraints;

use App\Core\Application\Validator\Constraints\IsUnique;
use App\Core\Application\Validator\UniqueConstraintCheckerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class IsUniqueValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UniqueConstraintCheckerInterface $uniqueConstraintChecker,
        private readonly PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsUnique) {
            throw new UnexpectedTypeException($constraint, IsUnique::class);
        }

        // La valeur vide est gérée par d'autres contraintes comme NotBlank
        if (null === $value || '' === $value) {
            return;
        }

        // Assurez-vous que la valeur est de type string pour la plupart des vérifications d'unicité
        if (!\is_string($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        $excludeId = null;
        // Si la contrainte spécifie une propriété pour l'ID (ex: pour les updates)
        if (null !== $constraint->idProperty) {
            $object = $this->context->getObject(); // L'objet DTO en cours de validation
            if (null !== $object) {
                try {
                    // Récupère la valeur de la propriété ID (ex: userId dans CreateUserDTO)
                    $excludeId = (string) $this->propertyAccessor->getValue($object, $constraint->idProperty);
                } catch (\Exception) {
                    // Gérer l'exception si la propriété n'existe pas ou n'est pas accessible
                    // Cela peut indiquer une erreur de configuration de la contrainte.
                    // Pour l'instant, on laisse excludeId à null.
                }
            }
        }

        // Délègue la vérification au GlobalUniqueConstraintChecker
        if (!$this->uniqueConstraintChecker->isUnique($constraint->entityContext, $constraint->constraintType, $value, $excludeId)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(IsUnique::NOT_UNIQUE_ERROR)
                ->addViolation();
        }
    }
}
