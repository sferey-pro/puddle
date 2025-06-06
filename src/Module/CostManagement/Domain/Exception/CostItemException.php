<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Exception de base pour toutes les erreurs métier liées à l'agrégat CostItem.
 * Cette classe utilise des constructeurs statiques nommés pour fournir des
 * messages d'erreur contextuels et clairs, tout en réduisant le nombre de classes d'exception.
 */
final class CostItemException extends \DomainException
{
    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function notFoundWithId(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" was not found.', $id));
    }

    public static function contributionNotFound(CostContributionId $id): self
    {
        return new self(\sprintf('CostContribution with ID "%s" was not found in this aggregate.', $id));
    }

    public static function alreadyArchived(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" is already archived.', $id));
    }

    public static function notArchived(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" is not archived, cannot reactivate.', $id));
    }

    public static function cannotBeArchived(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" cannot be archived due to current business rules.', $id));
    }

    public static function cannotBeReactivated(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" cannot be reactivated because its coverage period has ended or it is not archived.', $id));
    }

    public static function cannotReceiveContributionBecauseStatusIs(CostItemId $id, CostItemStatus $status): self
    {
        return new self(
            \sprintf(
                'CostItem with ID "%s" cannot receive contribution due to its status: %s.',
                $id,
                $status->value
            )
        );
    }

    public static function detailsUpdateNotAllowed(CostItemId $id, CostItemStatus $status): self
    {
        return new self(\sprintf('Details of CostItem "%s" cannot be updated while its status is "%s".', $id, $status->value));
    }

    public static function targetAmountConflict(Money $newTarget, Money $currentAmount): self
    {
        return new self(
            \sprintf(
                'New target amount (%s) cannot be less than the current covered amount (%s).',
                (string) $newTarget,
                (string) $currentAmount
            )
        );
    }
}
