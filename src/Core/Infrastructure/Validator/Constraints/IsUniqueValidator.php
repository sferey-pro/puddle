<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Validator\Constraints;

use App\Core\Application\Validator\Constraints\IsUnique;
use App\Core\Domain\Repository\SpecificationRepositoryInterface;
use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Domain\ValueObject\UniqueValueInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Rôle : Validateur pour la contrainte IsUnique.
 * C'est l'implémentation ("Adapter") qui connecte le besoin de validation
 * à l'infrastructure de persistance via le Pattern Spécification.
 */
final class IsUniqueValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsUnique) {
            throw new UnexpectedTypeException($constraint, IsUnique::class);
        }

        // On ne valide pas les valeurs vides, c'est le rôle de NotBlank/NotNull.
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof UniqueValueInterface) {
            throw new ConstraintDefinitionException(\sprintf('The value for IsUnique constraint must implement %s.', UniqueValueInterface::class));
        }

        // 1. On récupère le repository correspondant à l'entité demandée.
        $repository = $this->registry->getRepository($constraint->entityClass);

        // 2. On vérifie que le repository est capable de gérer les Spécifications.
        if (!$repository instanceof SpecificationRepositoryInterface) {
            throw new ConstraintDefinitionException(\sprintf('The repository for "%s" must implement %s to be used with IsUnique constraint.', $constraint->entityClass, SpecificationRepositoryInterface::class));
        }

        // 3. On gère le cas d'exclusion (pour les mises à jour).
        $excludeId = null;
        if (null !== $constraint->excludeIdField) {
            $rootObject = $this->context->getRoot();
            if ($this->propertyAccessor->isReadable($rootObject, $constraint->excludeIdField)) {
                $excludeIdValue = $this->propertyAccessor->getValue($rootObject, $constraint->excludeIdField);
                // On s'assure de ne pas exclure une valeur nulle (cas d'une création où l'ID est null).
                if (null !== $excludeIdValue) {
                    $excludeId = (string) $excludeIdValue;
                }
            }
        }

        // 4. On construit l'objet Spécification qui représente notre règle métier.
        $specification = new IsUniqueSpecification(
            $value,
            $excludeId
        );

        // 5. On demande au repository de compter les entités qui satisfont cette règle.
        if ($repository->countBySpecification($specification) > 0) {
            // 6. Si le compte est > 0, un doublon existe, on lève une violation.
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', (string) $value)
                ->setCode(IsUnique::NOT_UNIQUE_ERROR)
                ->addViolation();
        }
    }
}
