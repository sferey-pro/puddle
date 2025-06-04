<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\Query;

use App\Shared\Application\Query\QueryInterface;

final readonly class ListProductsQuery implements QueryInterface
{
    public function __construct(
        public ?int $page = null,
        public ?int $itemsPerPage = null,
        // public ?string $searchTerm = null, // Optionnel pour des filtres/recherches
    ) {
    }
}
