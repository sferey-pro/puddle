<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Tests\Story;

use App\Module\ProductCatalog\Domain\ValueObject\BaseCostStructure;
use App\Module\ProductCatalog\Domain\ValueObject\CostComponentLine;
use App\Module\ProductCatalog\Domain\ValueObject\CostComponentType;
use App\Module\ProductCatalog\Domain\ValueObject\ProductName;
use App\Module\ProductCatalog\Domain\ValueObject\Quantity;
use App\Module\ProductCatalog\Domain\ValueObject\UnitOfMeasure;
use App\Module\ProductCatalog\Tests\Factory\ProductFactory;
use App\Module\SharedContext\Domain\ValueObject\Money;
use Zenstruck\Foundry\Story;

// use Zenstruck\Foundry\Attribute\AsFixture;

// #[AsFixture(name: 'product-catalog')]
final class ProductStory extends Story
{
    public function build(): void
    {
        // Créer quelques produits spécifiques pour les tests
        ProductFactory::createOne([
            'name' => new ProductName('Espresso Classique v1'),
            'isActive' => true,
            'baseCostStructure' => new BaseCostStructure([ // Surcharge des composants de coût
                new CostComponentLine(
                    'Café Arabica 100%',
                    CostComponentType::RAW_MATERIAL,
                    Money::fromFloat(0.40, 'EUR'),
                    new Quantity(18, UnitOfMeasure::GRAM)
                ),
                new CostComponentLine(
                    'Gobelet carton',
                    CostComponentType::PACKAGING,
                    Money::fromFloat(0.10, 'EUR'),
                    new Quantity(1, UnitOfMeasure::PIECE)
                ),
                new CostComponentLine(
                    'Part Loyer Espresso',
                    CostComponentType::OPERATIONAL_FIXED_ALLOCATED,
                    Money::fromFloat(0.60, 'EUR')
                ),
                new CostComponentLine(
                    'Marge Espresso',
                    CostComponentType::MARGIN_CONTINGENCY,
                    Money::fromFloat(0.90, 'EUR') // Pour un prix de vente cible de 2.00€
                ),
            ]),
        ]);

        ProductFactory::createOne([
            'name' => new ProductName('Cappuccino Gourmand'),
            'isActive' => true,
            'baseCostStructure' => new BaseCostStructure([
                new CostComponentLine(
                    'Café Arabica 100% (double dose)',
                    CostComponentType::RAW_MATERIAL,
                    Money::fromFloat(0.80, 'EUR'),
                    new Quantity(36, UnitOfMeasure::GRAM)
                ),
                new CostComponentLine(
                    'Lait entier bio',
                    CostComponentType::RAW_MATERIAL,
                    Money::fromFloat(0.50, 'EUR'),
                    new Quantity(150, UnitOfMeasure::MILLILITER)
                ),
                new CostComponentLine(
                    'Gobelet carton grand',
                    CostComponentType::PACKAGING,
                    Money::fromFloat(0.15, 'EUR'),
                    new Quantity(1, UnitOfMeasure::PIECE)
                ),
                new CostComponentLine(
                    'Part Loyer Cappuccino',
                    CostComponentType::OPERATIONAL_FIXED_ALLOCATED,
                    Money::fromFloat(0.75, 'EUR')
                ),
                new CostComponentLine(
                    'Marge Cappuccino',
                    CostComponentType::MARGIN_CONTINGENCY,
                    Money::fromFloat(1.30, 'EUR') // Pour un prix de vente cible de 3.50€
                ),
            ]),
        ]);

        ProductFactory::createOne([
            'name' => new ProductName('Thé Vert Sencha'),
            'isActive' => false, // Produit inactif
            'baseCostStructure' => new BaseCostStructure([
                new CostComponentLine(
                    'Sachet Thé Vert Sencha Bio',
                    CostComponentType::RAW_MATERIAL,
                    Money::fromFloat(0.60, 'EUR'),
                    new Quantity(1, UnitOfMeasure::PIECE)
                ),
                new CostComponentLine(
                    'Eau filtrée',
                    CostComponentType::RAW_MATERIAL,
                    Money::fromFloat(0.05, 'EUR'),
                    new Quantity(200, UnitOfMeasure::MILLILITER)
                ),
                new CostComponentLine(
                    'Gobelet carton',
                    CostComponentType::PACKAGING,
                    Money::fromFloat(0.10, 'EUR'),
                    new Quantity(1, UnitOfMeasure::PIECE)
                ),
            ]),
        ]);

        // Créer plusieurs produits avec des valeurs par défaut (aléatoires définies dans la factory)
        ProductFactory::createMany(5);
    }
}
