<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\DTO;

/**
 * DTO public utilisé comme contrat de données pour exposer les informations
 * de base d'un produit à d'autres modules.
 */
final class ProductInfoDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $priceAmount,
        public readonly string $priceCurrency,
    ) {
    }
}
