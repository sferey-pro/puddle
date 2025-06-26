<?php

declare(strict_types=1);

namespace App\Core\Application\Validator\Constraints;

use App\Core\Infrastructure\Validator\Constraints\IsUniqueValidator;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * Contrainte qui vérifie si une valeur pour un champ donné est unique
 * pour une entité spécifique, avec la possibilité d'exclure un ID
 * (essentiel pour les opérations de mise à jour).
 *
 * Exemple d'utilisation :
 * #[CoreAssert\IsUnique(entityClass: UserAccount::class)]
 *
 * Exemple (mise à jour) :
 * #[CoreAssert\IsUnique(entityClass: UserAccount::class, excludeIdField: 'id')]
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
final class IsUnique extends Constraint
{
    public const NOT_UNIQUE_ERROR = 'f74577f8-3e4b-4860-9844-9c5950882e3c';

    protected const ERROR_NAMES = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public string $message = 'Cette valeur est déjà utilisée.';

    #[HasNamedArguments]
    public function __construct(
        public string $entityClass,
        public ?string $excludeIdField = null, // Nom de la propriété qui contient l'ID de l'entité (pour exclusion lors des updates)
        ?string $message = null,
        ?array $groups = null,
        $payload = null,
        ?array $options = null,
    ) {
        $options = array_merge([
            'entityClass' => $entityClass,
        ], $options ?? []);

        parent::__construct($options, $groups, $payload);
        $this->message = $message ?? $this->message;
    }

    public function validatedBy(): string
    {
        return IsUniqueValidator::class;
    }

    public function getRequiredOptions(): array
    {
        return ['entityClass'];
    }

    /**
     * Permet à la contrainte d'être appliquée sur une propriété, une méthode ou une classe.
     * En général, on l'appliquera sur la propriété concernée (ex: email) ou sur la classe si
     * la validation nécessite plusieurs propriétés du DTO (ex: couple (nom, prénom)).
     * Mais pour l'unicité simple, la propriété est suffisante.
     */
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
