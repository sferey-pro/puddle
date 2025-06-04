<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Tests\Factory;

use App\Module\ProductCatalog\Domain\Product;
use App\Module\ProductCatalog\Domain\ValueObject\BaseCostStructure;
use App\Module\ProductCatalog\Domain\ValueObject\CostComponentLine;
use App\Module\ProductCatalog\Domain\ValueObject\CostComponentType;
use App\Module\ProductCatalog\Domain\ValueObject\ProductId;
use App\Module\ProductCatalog\Domain\ValueObject\ProductName;
use App\Module\ProductCatalog\Domain\ValueObject\Quantity;
use App\Module\ProductCatalog\Domain\ValueObject\UnitOfMeasure;
use App\Module\SharedContext\Domain\ValueObject\Money;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use function Zenstruck\Foundry\faker;

/**
 * @extends PersistentProxyObjectFactory<Product>
 */
final class ProductFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Product::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->instantiateWith(static function (array $attributes): Product {
                // Récupérer ou générer les valeurs pour les arguments de Product::create()

                $productId = $attributes['identifier'] ?? ProductId::generate();
                $productName = (isset($attributes['name']) && $attributes['name'] instanceof ProductName) ? $attributes['name'] : new ProductName($attributes['name'] ?? faker()->words(2, true).' Café');

                // Gestion de baseCostStructure:
                // Si elle est fournie en tant qu'objet, on l'utilise, sinon on crée une structure par défaut.
                if (isset($attributes['baseCostStructure']) && $attributes['baseCostStructure'] instanceof BaseCostStructure) {
                    $baseCostStructure = $attributes['baseCostStructure'];
                } else {
                    // Création d'une structure de coût par défaut si non fournie
                    $defaultCostComponentLines = [
                        new CostComponentLine(
                            name: 'Grain de café par défaut',
                            type: CostComponentType::RAW_MATERIAL,
                            cost: Money::fromFloat(faker()->randomFloat(2, 0, 1), 'EUR'),
                            quantity: new Quantity(faker()->numberBetween(7, 20), UnitOfMeasure::GRAM)
                        ),
                        new CostComponentLine(
                            name: 'Part fixe par défaut',
                            type: CostComponentType::OPERATIONAL_FIXED_ALLOCATED,
                            cost: Money::fromFloat(faker()->randomFloat(2, 0, 1), 'EUR')
                        ),
                    ];

                    // Si $attributes['costComponents'] est un tableau de CostComponentLine, on l'utilise
                    if (isset($attributes['costComponents']) && \is_array($attributes['costComponents'])) {
                        $componentLines = [];
                        foreach ($attributes['costComponents'] as $componentData) {
                            if ($componentData instanceof CostComponentLine) {
                                $componentLines[] = $componentData;
                            } elseif (\is_array($componentData)) { // Si c'est un tableau de données pour un CostComponentLine
                                $quantity = null;
                                if (isset($componentData['quantityValue'], $componentData['quantityUnit']) && UnitOfMeasure::tryFrom($componentData['quantityUnit'])) {
                                    $quantity = new Quantity((float) $componentData['quantityValue'], UnitOfMeasure::from($componentData['quantityUnit']));
                                }
                                $componentLines[] = new CostComponentLine(
                                    $componentData['name'],
                                    CostComponentType::from($componentData['type']),
                                    Money::fromFloat((float) $componentData['costAmount'], $componentData['costCurrency'] ?? 'EUR'),
                                    $quantity
                                );
                            }
                        }
                        $baseCostStructure = new BaseCostStructure($componentLines ?: $defaultCostComponentLines);
                    } else {
                        $baseCostStructure = new BaseCostStructure($defaultCostComponentLines);
                    }
                }

                $product = Product::create(
                    identifier: $productId,
                    name: $productName,
                    baseCostStructure: $baseCostStructure
                );

                if (isset($attributes['isActive']) && false === $attributes['isActive']) {
                    $product->deactivate();
                } elseif (!isset($attributes['isActive']) && !faker()->boolean(80)) { // 80% de chance d'être actif par défaut
                    $product->deactivate();
                }

                // Si isActive est true ou non défini et que le booléen est true, il est déjà actif par défaut.
                return $product;
            })
        ;
    }
}
