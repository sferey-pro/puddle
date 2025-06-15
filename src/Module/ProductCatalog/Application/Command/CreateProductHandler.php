<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\Command;

use App\Module\ProductCatalog\Domain\Enum\CostComponentType;
use App\Module\ProductCatalog\Domain\Enum\UnitOfMeasure;
use App\Module\ProductCatalog\Domain\Product;
use App\Module\ProductCatalog\Domain\Repository\ProductRepositoryInterface;
use App\Module\ProductCatalog\Domain\ValueObject\BaseCostStructure;
use App\Module\ProductCatalog\Domain\ValueObject\CostComponentLine;
use App\Module\ProductCatalog\Domain\ValueObject\ProductName;
use App\Module\ProductCatalog\Domain\ValueObject\Quantity;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommandHandler]
final class CreateProductHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private EventBusInterface $eventBus,
        private ProductRepositoryInterface $repository,
    ) {
    }

    public function __invoke(CreateProduct $command): void
    {
        $dto = $command->dto;

        $costComponentLines = [];
        foreach ($dto->costComponents as $componentDTO) {
            $quantity = null;
            if (null !== $componentDTO->quantityValue && null !== $componentDTO->quantityUnit) {
                $unitOfMeasure = UnitOfMeasure::tryFrom($componentDTO->quantityUnit);
                if ($unitOfMeasure) { // Gérer le cas où tryFrom retourne null si la valeur n'est pas valide
                    $quantity = new Quantity($componentDTO->quantityValue, $unitOfMeasure);
                } else {
                    // Gérer l'erreur: unité invalide non détectée par la validation du formulaire ?
                    // Cela ne devrait pas arriver si la validation du formulaire est correcte.
                    throw new \InvalidArgumentException("Invalid unit of measure: {$componentDTO->quantityUnit}");
                }
            }

            $costComponentType = CostComponentType::tryFrom($componentDTO->type);
            if (!$costComponentType) {
                throw new \InvalidArgumentException("Invalid cost component type: {$componentDTO->type}");
            }

            $costComponentLines[] = new CostComponentLine(
                name: $componentDTO->name,
                type: $costComponentType,
                cost: Money::fromFloat($componentDTO->costAmount, $componentDTO->costCurrency),
                quantity: $quantity
            );
        }

        $baseCostStructure = new BaseCostStructure($costComponentLines);
        $productid = ProductId::generate(); // Génère un nouvel ID

        $product = Product::create(
            id: $productid,
            name: new ProductName($dto->name),
            baseCostStructure: $baseCostStructure
        );

        if (!$dto->isActive) {
            $product->deactivate();
        }

        $this->repository->save($product, true);

        // Dispatch domain events
        foreach ($product->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
