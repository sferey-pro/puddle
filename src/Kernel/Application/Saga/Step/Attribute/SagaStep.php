<?php

declare(strict_types=1);

namespace Kernel\Application\Saga\Step\Attribute;

/**
 * Attribut pour marquer une étape de Saga et définir sa transition associée.
 *
 * Permet de créer un mapping explicite entre une classe Step et une transition
 * du workflow, évitant toute dépendance à une convention de nommage.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class SagaStep
{
    public function __construct(
        public string $transitionName,
        public ?string $sagaType = null, // Optionnel : pour limiter à un type de saga spécifique
    ) {
    }
}
