<?php

declare(strict_types=1);

namespace App\Core\Application\Validator;

/**
 * Définit le contrat générique pour la vérification de l'unicité de données
 * métier essentielles à travers différents contextes (ex: un email pour un utilisateur,
 * un nom pour un produit). Ce service garantit que des invariants métier fondamentaux,
 * comme l'unicité d'une référence, sont respectés.
 */
interface UniqueConstraintCheckerInterface
{
    /**
     * Vérifie si une valeur donnée est unique pour un type de contrainte spécifique
     * au sein d'un contexte d'entité désigné.
     *
     * @param string      $entityContext  identifie le domaine ou le type d'entité concerné
     *                                    (ex: 'user' pour les utilisateurs, 'product' pour les produits)
     * @param string      $constraintType identifie le type de contrainte d'unicité à vérifier
     *                                    (ex: 'email' pour l'adresse email, 'displayName' pour le nom affiché)
     * @param mixed       $value          la valeur métier à vérifier pour son unicité
     * @param string|null $excludeId      Un identifiant optionnel de l'entité à exclure de la vérification.
     *                                    Utile lors de la mise à jour d'une entité existante où sa propre valeur ne doit pas être considérée comme un doublon.
     *
     * @return bool vrai si la valeur est unique, faux sinon
     */
    public function isUnique(string $entityContext, string $constraintType, mixed $value, ?string $excludeId = null): bool;
}
