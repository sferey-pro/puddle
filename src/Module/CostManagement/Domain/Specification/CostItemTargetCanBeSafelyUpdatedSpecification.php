<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Spécification qui garantit que le montant cible d'un CostItem peut être mis à jour en toute sécurité.
 *
 * Une mise à jour est considérée comme sûre si le nouveau montant cible n'est pas inférieur
 * au montant déjà couvert. Cela prévient un état incohérent où un item serait
 * "sur-couvert" par rapport à sa nouvelle cible.
 *
 * @template-extends AbstractSpecification<CostItem>
 */
final class CostItemTargetCanBeSafelyUpdatedSpecification extends AbstractSpecification
{
    public function __construct(private readonly Money $newTargetAmount) {
    }

    /**
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        // Le nouveau montant cible doit être supérieur ou égal au montant actuel.
        return $this->newTargetAmount->isGreaterThanOrEqual($candidate->currentAmountCovered());
    }
}
