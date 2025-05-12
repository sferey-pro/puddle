<?php

declare(strict_types=1);

namespace DataFixtures;

use App\Entity\Product;
use App\Entity\RawMaterialList;
use App\Entity\Supplier;
use App\Entity\User;
use App\Factory\AdditionalCostFactory;
use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use App\Factory\RawMaterialFactory;
use App\Factory\RawMaterialListFactory;
use App\Factory\SupplierFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = UserFactory::new()->withRoleAdmin()->create();

        /** @var Supplier $supplier */
        // $supplier = SupplierFactory::new(['name' => 'Carrefour'])
        //     ->reuse($user)
        //     ->create();

        // $this->loadRawMaterials($user, $supplier);
        // $this->loadAdditionalCosts($user);
        // $this->loadProducts($user);
        // $this->loadProductsIngredients();
    }

    public function getDependencies(): array
    {
        return [
            UserAdminFixtures::class,
        ];
    }

    protected function loadAdditionalCosts(User $createdBy): array
    {
        // Create AdditionalCost
        return AdditionalCostFactory::new()
            ->reuse($createdBy)
            ->sequence(
                [
                    ['name' => 'éléctricité'],
                    ['name' => 'eau'],
                    ['name' => 'impôts'],
                ]
            )
            ->create();
    }

    protected function loadRawMaterials(User $createdBy, Supplier $supplier): void
    {
        $category = CategoryFactory::new(['name' => 'Laits et Boissons végétales'])
            ->reuse($createdBy)
            ->create();

        // Create RawMaterial
        RawMaterialFactory::new()
            ->reuse($createdBy)
            ->reuse($category)
            ->reuse($supplier)
            ->sequence(
                [
                    [
                        'name' => 'Lait sans lactose 1% MG CANDIA',
                        'link' => 'https://www.carrefour.fr/p/lait-sans-lactose-1-mg-candia-3533636760576',
                        'priceVariability' => true,
                        'totalCost' => 11.35,
                        'unit' => 'L',
                        'unitPrice' => 1.42,
                    ],
                    [
                        'name' => 'Lait Sans lactose UHT Ecrémé Matin Léger LACTEL',
                        'link' => 'https://www.carrefour.fr/p/lait-sans-lactose-uht-ecreme-matin-leger-lactel-3428272040062',
                        'priceVariability' => true,
                        'totalCost' => 11.70,
                        'unit' => 'L',
                        'unitPrice' => 1.95,
                    ]
                ]
            )
            ->create();

        $category = CategoryFactory::new(['name' => 'Cafés en grains'])
            ->reuse($createdBy)
            ->create();

        RawMaterialFactory::new()
            ->reuse($createdBy)
            ->reuse($category)
            ->reuse($supplier)
            ->sequence(
                [
                    [
                        'name' => 'Café grains pur arabica Amérique Latine Bio CARREFOUR BIO',
                        'link' => 'https://www.carrefour.fr/p/cafe-grains-pur-arabica-amerique-latine-bio-carrefour-bio-3560071244057',
                        'priceVariability' => true,
                        'totalCost' => 9.49,
                        'unit' => 'Kg',
                        'unitPrice' => 18.98,
                    ],
                    [
                        'name' => 'Café en grains MALONGO',
                        'link' => 'http://carrefour.fr/p/cafe-en-grains-malongo-3187570072488',
                        'priceVariability' => true,
                        'totalCost' => 9.25,
                        'unit' => 'Kg',
                        'unitPrice' => 18.50,
                    ],
                ]
            )
            ->create();
    }

    protected function loadProducts(User $createdBy): array
    {
        $categoryHotDrink = CategoryFactory::new(['name' => 'Boisson chaude'])
            ->reuse($createdBy)
            ->create();

        // Create Product
        return ProductFactory::new()
            ->reuse($createdBy)
            ->reuse($categoryHotDrink)
            ->sequence(
                [
                    [
                        'name' => 'Cappucino',
                        'price' => 4,
                    ]
                ]
            )
            ->create();
    }

    protected function loadProductsIngredients(): void
    {
        $category = CategoryFactory::findBy(['slug' => 'laits-et-boissons-vegetales']);
        $rawMaterial = RawMaterialFactory::random(['category' => $category]);

        $product = ProductFactory::findBy(['slug' => 'cappucino']);

        RawMaterialListFactory::new()
            ->reuse($product[0])
            ->create([
                'rawMaterialItems' => [
                    $rawMaterial,
                ]
            ]);
    }

    protected function loadSuppliers(User $createdBy): void
    {
        // Create Supplier
        SupplierFactory::new()
            ->reuse($createdBy)
            ->sequence([
                    ['name' => 'supplier 1'],
                    ['name' => 'supplier 2'],
            ])
            ->create();
    }
}
