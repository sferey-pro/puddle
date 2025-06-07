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
    private const NOT_FOUND = 'CM-001';
    private const CONTRIBUTION_NOT_FOUND = 'CM-003';
    private const ALREADY_ARCHIVED = 'CM-004';
    private const NOT_ARCHIVED = 'CM-005';
    private const CANNOT_BE_ARCHIVED = 'CM-006';
    private const CANNOT_BE_REACTIVATE = 'CM-007';
    private const CANNOT_RECEIVE_CONTRIBUTION = 'CM-008';
    private const DETAILS_UPDATE_NOT_ALLOWED = 'CM-009';
    private const TARGET_AMOUNT_CONFLICT = 'CM-010';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode)
    {
        parent::__construct($message);
    }

    public static function notFoundWithId(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" was not found.', $id), self::NOT_FOUND);
    }

    public static function contributionNotFound(CostContributionId $id): self
    {
        return new self(\sprintf('CostContribution with ID "%s" was not found in this aggregate.', $id), self::CONTRIBUTION_NOT_FOUND);
    }

    public static function alreadyArchived(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" is already archived.', $id), self::ALREADY_ARCHIVED);
    }

    public static function notArchived(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" is not archived, cannot reactivate.', $id), self::NOT_ARCHIVED);
    }

    public static function cannotBeArchived(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" cannot be archived due to current business rules.', $id), self::CANNOT_BE_ARCHIVED);
    }

    public static function cannotBeReactivated(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" cannot be reactivated because its coverage period has ended or it is not archived.', $id), self::CANNOT_BE_REACTIVATE);
    }

    public static function cannotReceiveContributionBecauseStatusIs(CostItemId $id, CostItemStatus $status): self
    {
        return new self(
            \sprintf(
                'CostItem with ID "%s" cannot receive contribution due to its status: %s.',
                $id,
                $status->value
            ),
            self::CANNOT_RECEIVE_CONTRIBUTION
        );
    }

    public static function detailsUpdateNotAllowed(CostItemId $id, CostItemStatus $status): self
    {
        return new self(\sprintf('Details of CostItem "%s" cannot be updated while its status is "%s".', $id, $status->value), self::DETAILS_UPDATE_NOT_ALLOWED);
    }

    public static function targetAmountConflict(Money $newTarget, Money $currentAmount): self
    {
        return new self(
            \sprintf(
                'New target amount (%s) cannot be less than the current covered amount (%s).',
                (string) $newTarget,
                (string) $currentAmount
            ),
            self::TARGET_AMOUNT_CONFLICT
        );
    }
}
