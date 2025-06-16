<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel;

use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Read Model spécifique pour les CostItems qui servent de modèle (template).
 * Il ne contient que les informations de configuration.
 */
class CostItemTemplateView
{
    public string $id;
    public string $name;
    public ?string $type = null;
    public float $targetAmount = 0.0;
    public string $currency = 'EUR';
    public string $status;

    /**
     * Le constructeur est privé pour forcer la création via le factory `fromCostItemAdded`.
     */
    private function __construct(
        string $id,
        string $name,
        ?string $type,
        float $targetAmount,
        string $currency,
        string $status,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->targetAmount = $targetAmount;
        $this->currency = $currency;
        $this->status = $status;
    }

    /**
     * Factory pour créer une nouvelle vue à partir de l'événement de création.
     */
    public static function fromCostItemAdded(CostItemAdded $event): self
    {
        return new self(
            id: (string) $event->costItemId(),
            name: (string) $event->name(),
            type: $event->type()->value,
            targetAmount: self::convertMoneyToFloat($event->targetAmount()),
            currency: $event->targetAmount()->currency(),
            status: $event->status()->value,
        );
    }

    /**
     * Factory pour créer une vue à partir d'un agrégat existant (pour la réconciliation).
     */
    public static function fromAggregate(CostItem $item): self
    {
        $view = new self(
            id: (string) $item->id(),
            name: (string) $item->name(),
            type: $item->type()->value,
            targetAmount: self::convertMoneyToFloat($item->targetAmount()),
            currency: $item->targetAmount()->currency(),
            status: $item->status()->value
        );

        return $view;
    }

    /**
     * Met à jour la vue à partir de l'état actuel de l'agrégat.
     */
    public function updateFromAggregate(CostItem $item): void
    {
        $this->name = (string) $item->name();
        $this->type = $item->type()->value;
        $this->targetAmount = self::convertMoneyToFloat($item->targetAmount());
        $this->currency = $item->targetAmount()->currency();
        $this->status = $item->status()->value;
    }

    /**
     * Applique les changements de l'événement de mise à jour des détails.
     */
    public function updateFromDetails(CostItemDetailsUpdated $event): void
    {
        $this->name = (string) $event->newName();
        $this->targetAmount = self::convertMoneyToFloat($event->newTargetAmount());
        $this->currency = $event->newTargetAmount()->currency();
    }

    /**
     * Met à jour le statut de la vue.
     */
    public function updateStatus(string $newStatus): void
    {
        $this->status = $newStatus;
    }

    /**
     * Utilitaire pour convertir un objet Money en float.
     */
    private static function convertMoneyToFloat(Money $money): float
    {
        return $money->toFloat();
    }
}
