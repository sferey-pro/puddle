<?php

declare(strict_types=1);

namespace App\Core\Specification;

/**
 * Interface pour le pattern Specification.
 *
 * Définit un contrat unique : une méthode qui détermine si un objet "candidat"
 * satisfait à une règle ou un critère métier spécifique.
 */
interface SpecificationInterface
{
    /**
     * Vérifie si le candidat donné satisfait à la spécification.
     *
     * @param mixed $candidate L'objet à valider.
     * @return bool Vrai si le candidat satisfait à la spécification, sinon faux.
     */
    public function isSatisfiedBy(mixed $candidate): bool;
}
