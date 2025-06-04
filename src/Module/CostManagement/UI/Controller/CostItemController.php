<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Controller;

use App\Module\CostManagement\Application\Command\CreateCostItem;
use App\Module\CostManagement\Application\DTO\CostComponentLineDTO;
use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;
use App\Module\CostManagement\Application\Query\FindCostItemQuery;
use App\Module\CostManagement\Application\Query\ListCostItemsQuery;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\UI\Form\CostItemFormType;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\Doctrine\Paginator;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

final class CostItemController extends AbstractController
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus,
    ) {
    }

    #[Template('@CostManagement/index.html.twig')]
    public function index(
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $page = 1,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $limit = Paginator::PAGE_SIZE,
    ): array {
        $productsPaginator = $this->queryBus->ask(new ListCostItemsQuery($page, $limit));

        return [
            'products' => $productsPaginator,
        ];
    }

    #[Template('@CostManagement/show.html.twig')]
    public function show(Request $request): array
    {
        $product = $this->queryBus->ask(new FindCostItemQuery(
            identifier: CostItemId::fromString($request->get('id'))
        ));

        return [
            'product' => $product,
        ];
    }

    #[Template('@CostManagement/new.html.twig')]
    public function new(Request $request): array|RedirectResponse
    {
        $dto = new CreateCostItemDTO();
        $dto->costComponents[] = new CostComponentLineDTO();

        $form = $this->createForm(CostItemFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->commandBus->dispatch(new CreateCostItem($dto));

                $this->addFlash('success', 'Produit créé avec succès !');

                return $this->redirectToRoute('cost_item_index'); // Nom de la route pour lister les cost items
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la création du produit.');
            }
        }

        return [
            'form' => $form,
            'dto' => $dto,
            'page_title' => 'Créer un nouveau Produit',
        ];
    }
}
