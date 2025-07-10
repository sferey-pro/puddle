<?php

declare(strict_types=1);

namespace Kernel\Application\Saga\Step;

use Kernel\Application\Saga\Step\Attribute\SagaStep;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * Registry centralisant les étapes de Saga pour le mapping transition -> step.
 */
final class SagaStepRegistry
{
    private array $steps = [];

    public function __construct(
        #[AutowireIterator('saga.step')]
        iterable $steps
    ) {
        foreach ($steps as $step) {
            $this->register($step);
        }
    }

    public function register(SagaStepInterface $step): void
    {
        $reflection = new \ReflectionClass($step);
        $sagaStepAttributes = $this->extractSagaStepAttributes($reflection);

        if (empty($sagaStepAttributes)) {
            throw new \LogicException(
                sprintf('Step %s must have at least one #[SagaStep] attribute.', $reflection->getName())
            );
        }

        // Une step peut avoir plusieurs attributs #[SagaStep] pour supporter plusieurs transitions/sagas
        foreach ($sagaStepAttributes as $sagaStepAttribute) {
            $key = $this->buildStepKey($sagaStepAttribute->transitionName, $sagaStepAttribute->sagaType);
            $this->steps[$key] = $step;
        }
    }

    public function getStep(string $transitionName, ?string $sagaType = null): ?SagaStepInterface
    {
        // 1. Chercher d'abord une step spécifique au type de saga
        if (null !== $sagaType) {
            $specificKey = $this->buildStepKey($transitionName, $sagaType);
            if (isset($this->steps[$specificKey])) {
                return $this->steps[$specificKey];
            }
        }

        // 2. Fallback : chercher une step universelle (sagaType = null)
        $universalKey = $this->buildStepKey($transitionName, null);
        return $this->steps[$universalKey] ?? null;
    }

    public function hasStep(string $transitionName, ?string $sagaType = null): bool
    {
        // 1. Vérifier d'abord si une step spécifique existe
        if (null !== $sagaType) {
            $specificKey = $this->buildStepKey($transitionName, $sagaType);
            if (isset($this->steps[$specificKey])) {
                return true;
            }
        }

        // 2. Vérifier si une step universelle existe
        $universalKey = $this->buildStepKey($transitionName, null);
        return isset($this->steps[$universalKey]);
    }

    /**
     * @return SagaStep[]
     */
    private function extractSagaStepAttributes(\ReflectionClass $reflection): array
    {
        $attributes = $reflection->getAttributes(SagaStep::class);

        $sagaStepAttributes = [];
        foreach ($attributes as $attribute) {
            $sagaStepAttributes[] = $attribute->newInstance();
        }

        return $sagaStepAttributes;
    }

    /**
     * Génère une clé unique pour le mapping step basée sur transition + sagaType.
     */
    private function buildStepKey(string $transitionName, ?string $sagaType): string
    {
        return null === $sagaType
            ? $transitionName  // Step universelle
            : sprintf('%s:%s', $sagaType, $transitionName); // Step spécifique
    }
}
