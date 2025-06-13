<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\UI\Controller;

use App\Module\ProductCatalog\Application\Command\CreateProduct;
use App\Module\ProductCatalog\Application\DTO\CostComponentLineDTO;
use App\Module\ProductCatalog\Application\DTO\CreateProductDTO;
use App\Module\ProductCatalog\Application\Query\FindProductQuery;
use App\Module\ProductCatalog\Application\Query\ListProductsQuery;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Module\ProductCatalog\UI\Form\ProductFormType;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\Doctrine\Paginator;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

final class ProductController extends AbstractController
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus,
    ) {
    }

    #[Template('@ProductCatalog/product/index.html.twig')]
    public function index(
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $page = 1,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $limit = Paginator::PAGE_SIZE,
    ): array {
        $productsPaginator = $this->queryBus->ask(new ListProductsQuery($page, $limit));

        return [
            'products' => $productsPaginator,
        ];
    }

    #[Template('@ProductCatalog/product/show.html.twig')]
    public function show(Request $request): array
    {
        $product = $this->queryBus->ask(new FindProductQuery(
            identifier: ProductId::fromString($request->get('id'))
        ));

        return [
            'product' => $product,
        ];
    }

    #[Template('@ProductCatalog/product/new.html.twig')]
    public function new(Request $request): array|RedirectResponse
    {
        $dto = new CreateProductDTO();
        $dto->costComponents[] = new CostComponentLineDTO();

        $form = $this->createForm(ProductFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->commandBus->dispatch(new CreateProduct($dto));

                $this->addFlash('success', 'Produit créé avec succès !');

                return $this->redirectToRoute('product_index'); // Nom de la route pour lister les produits
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la création du produit.');
            }
        }

        return [
            'form' => $form,
            'formDto' => $dto
        ];
    }

    public function edit(Request $request): array|RedirectResponse
    {
        $product = $this->queryBus->ask(new FindProductQuery(
            identifier: ProductId::fromString($request->get('id'))
        ));

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        return [
            'form' => $form,
            'formDto' => $product
        ];
    }
}
