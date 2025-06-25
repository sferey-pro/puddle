<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;
use App\Module\CostManagement\Application\ReadModel\RecurringCostView;
use Doctrine\ODM\MongoDB\DocumentManager;

#[AsQueryHandler]
final class ListRecurringCostsQueryHandler
{
    public function __construct(
        private readonly DocumentManager $documentManager,
    ) {
    }

    /**
     * @return RecurringCostView[]
     */
    public function __invoke(ListRecurringCostsQuery $query): array
    {
        return $this->documentManager
            ->getRepository(RecurringCostView::class)
            ->findBy([], ['createdAt' => 'DESC']);
    }
}
