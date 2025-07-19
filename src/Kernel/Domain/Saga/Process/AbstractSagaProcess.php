<?php

declare(strict_types=1);

namespace Kernel\Domain\Saga\Process;

use Kernel\Application\Saga\Process\SagaProcessInterface;
use Kernel\Domain\Contract\Entity\Timestampable;
use Kernel\Domain\Saga\SagaStateId;

/**
 * Fournit une structure de base pour suivre l'avancement d'un "Parcours métier" long.
 *
 * Rôle métier :
 * Chaque instance de cette classe représente un parcours en cours (par exemple, une
 * inscription d'utilisateur spécifique, une commande client particulière).
 * Elle sert de "dossier central" qui contient :
 * - L'identifiant unique du parcours.
 * - L'étape actuelle où se trouve le parcours (ex: "en_attente_validation_paiement").
 * - L'historique des étapes déjà validées.
 * - Toutes les informations métier nécessaires pour mener le parcours à son terme (le contexte).
 */
abstract class AbstractSagaProcess implements SagaProcessInterface,
    Timestampable
{
    // ==================== PROPRIÉTÉS PRINCIPALES ====================
    public SagaStateId $id;
    public string $currentState;
    private array $history = [];
    private array $context = [];


    // ==================== PROPRIÉTÉS POUR CONTRACTS ====================

    // Timestampable
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    private function __construct(
        SagaStateId $id
    ) {
        $this->id = $id;
    }

    public static function create() {
        return new static(SagaStateId::generate());
    }

    public function id(): SagaStateId
    {
        return $this->id;
    }

    // ==================== IMPLÉMENTATION TIMESTAMPABLE ====================

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    // ==================== MÉTHODES PRIVÉES ====================

    public function addTransitionToHistory(string $transitionName): void
    {
        $this->history[] = $transitionName;
    }

    public function history(): array
    {
        return array_reverse($this->history);
    }

    public function addToContext(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
    }

    /**
     * Retourne soit le context complet, soit une valeur spécifique du context.
     *
     * @param string|null $key La clé à récupérer. Si null, retourne tout le tableau.
     *
     * @return mixed la valeur de la clé, le tableau complet, ou null si la clé n'existe pas
     */
    public function context(?string $key = null): mixed
    {
        // Si aucune clé n'est fournie, on retourne tout le tableau.
        if (null === $key) {
            return $this->context;
        }

        // Si une clé est fournie, on retourne sa valeur, ou null si elle n'existe pas.
        return $this->context[$key] ?? null;
    }

    abstract public function getCurrentState(): string;

    abstract public function setCurrentState(string $currentState, array $context = []): void;
}
