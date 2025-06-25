<?php

declare(strict_types=1);

namespace App\Core\Domain\Specification;

/**
 * Interface pour le pattern Specification.
 *
 * Ce pattern permet d'encapsuler une règle métier qui peut être testée sur un objet (le "candidat").
 * Le but est de créer des règles métier composables et réutilisables. Une spécification
 * définit un contrat unique : une méthode qui détermine si un objet candidat satisfait
 * à un critère spécifique.
 *
 * @template T Le type de l'objet candidat que cette spécification évalue.
 */
interface SpecificationInterface
{
    /**
     * Vérifie si le candidat donné satisfait à la spécification.
     *
     * @param t $candidate L'objet à valider
     *
     * @return bool vrai si le candidat satisfait à la spécification, sinon faux
     */
    public function isSatisfiedBy(mixed $candidate): bool;

    /**
     * Retourne la raison pour laquelle la dernière évaluation a échoué.
     * Retourne null si la dernière évaluation a réussi.
     */
    public function getFailureReason(): ?string;
}
