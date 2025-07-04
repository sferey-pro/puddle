<?php

declare(strict_types=1);

namespace App\Core\Application\Saga\Step;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * Annuaire central de toutes les étapes possibles des parcours métier.
 *
 * Rôle métier :
 * Ce registre agit comme un catalogue de "procédures". Quand un parcours métier
 * (comme une inscription) doit passer à l'étape "envoyer_email_bienvenue",
 * c'est cet annuaire qui est consulté pour trouver la procédure exacte à exécuter.
 *
 * Il permet au système de connaître toutes les actions possibles et de les
 * orchestrer sans que les composants soient directement liés les uns aux autres.
 */
final readonly class SagaStepRegistry
{
    /** @var array<string, SagaStepInterface> */
    private array $steps;

    /**
     * Le constructeur reçoit la liste de toutes les étapes disponibles dans l'application.
     */
    public function __construct(
        #[AutowireIterator('saga.step', indexAttribute: 'transition')]
        iterable $steps,
    ) {
        $this->steps = $steps instanceof \Traversable ? iterator_to_array($steps) : $steps;
    }

    /**
     * Vérifie si une procédure nommée existe dans l'annuaire.
     */
    public function hasStep(string $transitionName): bool
    {
        return isset($this->steps[$transitionName]);
    }

    /**
     * Récupère l'étape correspondant à un nom donné.
     *
     * @throws \InvalidArgumentException si aucune procédure n'est trouvée pour ce nom
     */
    public function getStep(string $transitionName): SagaStepInterface
    {
        if (!$this->hasStep($transitionName)) {
            throw new \InvalidArgumentException(\sprintf('No saga step found for transition "%s".', $transitionName));
        }

        return $this->steps[$transitionName];
    }
}
