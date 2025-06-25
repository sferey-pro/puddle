<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\Query;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;
use App\Module\Sales\Application\ReadModel\Repository\OrderViewRepositoryInterface;

#[AsQueryHandler]
final class ListOrdersQueryHandler
{
    public function __construct(
        private readonly OrderViewRepositoryInterface $orderViewRepository,
    ) {
    }

    public function __invoke(ListOrdersQuery $query): array
    {
        return $this->orderViewRepository->findAll();
    }
}
