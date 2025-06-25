<?php

declare(strict_types=1);

namespace App\Core\Application\Validator\Constraints;

use App\Core\Infrastructure\Validator\Constraints\IsUniqueValidator;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * Contrainte de validation pour vérifier l'unicité d'une valeur dans un contexte métier donné.
 * Cette contrainte est utilisée pour les validations en amont, par exemple dans les formulaires,
 * avant que les données n'atteignent le domaine via les commandes.
 *
 * Exemple d'utilisation sur un DTO :
 * #[Assert\NotBlank]
 * #[Assert\Email]
 * #[IsUnique(entityContext: 'user', constraintType: 'email', message: 'Cette adresse email est déjà utilisée.')]
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
final class IsUnique extends Constraint
{
    public const NOT_UNIQUE_ERROR = 'f74577f8-3e4b-4860-9844-9c5950882e3c';

    protected const ERROR_NAMES = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public string $message = 'Cette valeur est déjà utilisée.';
    public string $entityContext;
    public string $constraintType;
    public ?string $idProperty = null; // Nom de la propriété qui contient l'ID de l'entité (pour exclusion lors des updates)

    /**
     * @param string               $entityContext  contexte de l'entité (ex: 'user', 'product')
     * @param string               $constraintType type de contrainte d'unicité (ex: 'email', 'name')
     * @param string|null          $message        message d'erreur personnalisé
     * @param string|null          $idProperty     nom de la propriété du DTO qui contient l'ID de l'entité
     * @param array<string>        $groups         groupes de validation
     * @param mixed                $payload        données additionnelles pour la validation
     * @param array<string, mixed> $options        tableau des options de la contrainte
     */
    #[HasNamedArguments]
    public function __construct(
        string $entityContext,
        string $constraintType,
        ?string $message = null,
        ?string $idProperty = null,
        ?array $groups = null,
        $payload = null,
        ?array $options = null,
    ) {
        $options = array_merge([
            'entityContext' => $entityContext,
            'constraintType' => $constraintType,
        ], $options ?? []);

        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->idProperty = $idProperty;
        $this->entityContext = $entityContext;
        $this->constraintType = $constraintType;
    }

    public function validatedBy(): string
    {
        return IsUniqueValidator::class;
    }

    public function getRequiredOptions(): array
    {
        return ['entityContext', 'constraintType'];
    }

    /**
     * Permet à la contrainte d'être appliquée sur une propriété, une méthode ou une classe.
     * En général, on l'appliquera sur la propriété concernée (ex: email) ou sur la classe si
     * la validation nécessite plusieurs propriétés du DTO (ex: couple (nom, prénom)).
     * Mais pour l'unicité simple, la propriété est suffisante.
     */
    public function getTargets(): string|array
    {
        return [self::PROPERTY_CONSTRAINT, self::CLASS_CONSTRAINT];
    }
}
