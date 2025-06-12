<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;

/**
 * Exception de base pour toutes les erreurs métier liées à l'agrégat RecurringCost.
 * Cette classe utilise des constructeurs statiques nommés pour fournir des
 * messages d'erreur contextuels et clairs, tout en réduisant le nombre de classes d'exception.
 */
final class RecurringCostException extends \DomainException
{
    private const NOT_FOUND = 'CM-001';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode)
    {
        parent::__construct($message);
    }

    public static function notFoundWithId(RecurringCostId $id): self
    {
        return new self(\sprintf('RecurringCost with ID "%s" was not found.', $id), self::NOT_FOUND);
    }
}
