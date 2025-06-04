<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\Hydration;

use App\Module\ProductCatalog\Application\DTO\CostComponentLineDTO;
use App\Module\ProductCatalog\Application\DTO\CreateProductDTO;
use Symfony\UX\LiveComponent\Hydration\HydrationExtensionInterface;

class CreateProductDTOHydration implements HydrationExtensionInterface
{
    public function __construct(
        private readonly CostComponentLineDTOHydration $costComponentLineHydrator,
    ) {
    }

    public function supports(string $className): bool
    {
        return is_a($className, CreateProductDTO::class, true);
    }

    public function hydrate(mixed $value, string $className): ?object
    {
        return new CreateProductDTO(
            name: $value['name'],
            costComponents: array_map(
                fn ($component) => !$this->costComponentLineHydrator->supports($component::class) ?:
                    $this->costComponentLineHydrator->hydrate($component, CostComponentLineDTO::class),
                $data['costComponents'] ?? []
            ),
            isActive: $value['isActive']
        );
    }

    public function dehydrate(object $object): mixed
    {
        return [
            'name' => $object->name,
            'costComponents' => array_map(
                fn ($component) => $this->costComponentLineHydrator->dehydrate($component),
                $object->costComponents
            ),
            'isActive' => $object->isActive,
        ];
    }
}
