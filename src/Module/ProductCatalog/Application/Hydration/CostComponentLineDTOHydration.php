<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\Hydration;

use App\Module\ProductCatalog\Application\DTO\CostComponentLineDTO;
use Symfony\UX\LiveComponent\Hydration\HydrationExtensionInterface;

class CostComponentLineDTOHydration implements HydrationExtensionInterface
{
    public function supports(string $className): bool
    {
        return is_a($className, CostComponentLineDTO::class, true);
    }

    public function hydrate(mixed $value, string $className): ?object
    {
        return new CostComponentLineDTO(
            name: $value['name'],
            type: $value['type'],
            costAmount: $value['costAmount'],
            costCurrency: $value['costCurrency'],
            quantityValue: $value['quantityValue'],
            quantityUnit: $value['quantityUnit'],
        );
    }

    public function dehydrate(object $object): mixed
    {
        return [
            'name' => $object->name,
            'type' => $object->type,
            'costAmount' => $object->costAmount,
            'costCurrency' => $object->costCurrency,
            'quantityValue' => $object->quantityValue,
            'quantityUnit' => $object->quantityUnit,
        ];
    }
}
