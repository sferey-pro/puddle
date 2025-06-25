<?php

declare(strict_types=1);

namespace App\Shared\Saga\Domain;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Saga\Domain\Enum\SagaStatus;
use App\Shared\Saga\Domain\Exception\SagaException;
use App\Shared\Saga\Domain\ValueObject\SagaStateId;

/**
 * Représente l'état persistant d'une instance de Saga.
 *
 * Cet agrégat est la "mémoire" d'un processus métier complexe (la Saga). Il ne contient
 * aucune logique métier *spécifique*, mais il sauvegarde la progression (quelle étape ?) et le
 * contexte (`payload`) nécessaires pour orchestrer les différentes actions à travers les modules.
 * C'est le garant de la reprise sur erreur d'un long processus.
 *
 * @author Puddle <puddle@puddle.com>
 */
class SagaState extends AggregateRoot
{
    private SagaStateId $id;

    /**
     * @var string Le nom unique qui identifie le type de processus métier (ex: 'user_registration').
     */
    private string $sagaType;

    private string $status;

    /**
     * @var int L'index de la dernière étape terminée avec succès.
     */
    private int $currentStep = 0;

    /**
     * @var array Le "sac de données" contenant le contexte métier qui transite entre les étapes (ex: userId, email).
     */
    private array $payload;

    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;
    private ?\DateTimeImmutable $completedAt = null;

    /**
     *  @var string|null En cas d'échec, stocke la raison pour le débogage.
     */
    private ?string $failureReason = null;


    private function __construct(
        string $sagaType,
        array $initialPayload,
    ) {
        $this->id = SagaStateId::generate();
        $this->sagaType = $sagaType;
        $this->payload = $initialPayload;
        $this->status = 'pending';
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }


    public static function create(string $sagaType, array $initialPayload): self
    {
        $sagaState = new self($sagaType, $initialPayload);

        return $sagaState;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Setter public requis par le composant Workflow pour mettre à jour l'état.
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();

        if ($status === SagaStatus::COMPLETED->value) {
            $this->completedAt = new \DateTimeImmutable();
        }
    }

    // --- Accesseurs ---
    public function id(): SagaStateId
    {
        return $this->id;
    }

    public function sagaType(): string
    {
        return $this->sagaType;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function currentStep(): int
    {
        return $this->currentStep;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function completedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function failureReason(): ?string
    {
        return $this->failureReason;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
