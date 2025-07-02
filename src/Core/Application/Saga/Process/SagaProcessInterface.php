<?php

declare(strict_types=1);

namespace App\Core\Application\Saga\Process;

/**
 * Définit le contrat pour un "Parcours métier" (Saga).
 *
 * Rôle métier :
 * Une Saga représente un parcours métier qui s'étend sur plusieurs étapes,
 * comme une inscription complète, une commande complexe ou une campagne marketing.
 *
 * Cette interface garantit que chaque "Parcours" peut :
 * - Fournir un identifiant unique pour le suivre (ex: "inscription_utilisateur_123").
 * - Conserver le contexte métier nécessaire à son exécution (ex: l'email de l'utilisateur, les produits commandés).
 * - Maintenir un historique des étapes déjà franchies.
 */
interface SagaProcessInterface
{
    /**
     * Retourne le type de parcours métier (ex: "inscription", "commande").
     */
    public static function sagaType(): string;

    /**
     * Accède aux informations du parcours en cours.
     *
     * @param string|null $key La donnée spécifique à récupérer (ex: "email").
     * @return mixed Les informations du parcours.
     */
    public function context(?string $key = null): mixed;

    /**
     * Ajoute une information au parcours métier.
     */
    public function addToContext(string $key, mixed $value): void;

    /**
     * Enregistre qu'une étape du parcours a été franchie avec succès.
     */
    public function addTransitionToHistory(string $transitionName): void;

    /**
     * Consulte l'historique des étapes déjà réalisées pour ce parcours.
     */
    public function history(): array;
}
