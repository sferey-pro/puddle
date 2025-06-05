<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel;

/**
 * Read model représentant un CostItem pour l'affichage.
 * Cette classe est intentionnellement mutable pour être mise à jour par le projecteur.
 */
class CostItemView
{
    public string $id;
    public string $name;
    public float $targetAmount;
    public float $currentAmount;
    public string $currency;
    public string $startDate;
    public string $endDate;
    public string $status;
    public float $progressPercentage;
    public bool $isCovered;
    public bool $isActiveNow;

    public function __construct(
        string $id,
        string $name,
        float $targetAmount,
        float $currentAmount,
        string $currency,
        string $startDate,
        string $endDate,
        string $status,
        float $progressPercentage,
        bool $isCovered,
        bool $isActiveNow,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->targetAmount = $targetAmount;
        $this->currentAmount = $currentAmount;
        $this->currency = $currency;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
        $this->progressPercentage = $progressPercentage;
        $this->isCovered = $isCovered;
        $this->isActiveNow = $isActiveNow;
    }
}
