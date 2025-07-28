<?php

declare(strict_types=1);

namespace Kernel\Application\Saga\Step;

use Kernel\Application\Saga\Process\SagaProcessInterface;

/**
 * Définit le contrat pour une "étape" d'un parcours métier (Saga).
 *
 * Rôle métier :
 * Chaque parcours métier est décomposé en étapes atomiques. Par exemple, une
 * inscription peut avoir les étapes : "Créer le compte", "Envoyer email de bienvenue", "Notifier le commercial".
 *
 * Cette interface garantit que chaque étape est capable de :
 * 1.  Réaliser son action principale (la méthode `execute`).
 * 2.  Annuler son action si une étape ultérieure du parcours échoue (la méthode `compensate`).
 *
 * Cette dualité est le cœur de la fiabilité d'une Saga : chaque action doit pouvoir être compensé en cas de fail.
 */
interface SagaStepInterface
{
    /**
     * Exécute la tâche principale de cette étape.
     * Par exemple : "Créer le compte utilisateur en base de données".
     *
     * @param SagaProcessInterface $sagaProcess le parcours métier en cours
     */
    public function execute(SagaProcessInterface $sagaProcess): void;

    /**
     * Annule la tâche effectuée par la méthode `execute`.
     * C'est l'action de "marche arrière" indispensable en cas d'échec global du parcours.
     * Par exemple : "Supprimer le compte utilisateur qui vient d'être créé".
     *
     * @param SagaProcessInterface $sagaProcess le parcours métier en cours
     */
    public function compensate(SagaProcessInterface $sagaProcess): void;
}
