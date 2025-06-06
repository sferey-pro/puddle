<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Controller;

use App\Module\CostManagement\Application\Command\ArchiveCostItem;
use App\Module\CostManagement\Application\Command\UpdateCostItem;
use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;
use App\Module\CostManagement\Application\DTO\UpdateCostItemDTO;
use App\Module\CostManagement\Application\Query\FindCostItemQuery;
use App\Module\CostManagement\Application\Query\ListCostItemsQuery;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\UI\Form\CostItemFormType;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\Doctrine\Paginator;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

final class CostItemController extends AbstractController
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    #[Template('@CostManagement/index.html.twig')]
    public function index(
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $page = 1,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $limit = Paginator::PAGE_SIZE,
    ): array {
        $costItemsPaginator = $this->queryBus->ask(new ListCostItemsQuery($page, $limit));

        return [
            'costItems' => $costItemsPaginator,
        ];
    }

    #[Template('@CostManagement/show.html.twig')]
    public function show(Request $request): array
    {
        $costItem = $this->queryBus->ask(new FindCostItemQuery(
            identifier: CostItemId::fromString($request->get('id'))
        ));

        return [
            'costItem' => $costItem,
        ];
    }

    #[Template('@CostManagement/new.html.twig')]
    public function new(): array
    {
        return [
            'formDto' => new CreateCostItemDTO(),
        ];
    }

    public function edit(Request $request, string $id): Response
    {
        /** @var CostItem $costItem */
        $costItem = $this->queryBus->ask(new FindCostItemQuery(CostItemId::fromString($id)));
        if (!$costItem) {
            throw $this->createNotFoundException('Le poste de coût n\'existe pas.');
        }

        $dto = UpdateCostItemDTO::fromCostItem($costItem);
        $form = $this->createForm(CostItemFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->dispatch(new UpdateCostItem($dto));
            $this->addFlash('success', 'Poste de coût mis à jour avec succès !');

            return $this->redirectToRoute('cost_item_show', ['id' => $id]);
        }

        return $this->render('@CostManagement/edit.html.twig', [
            'form' => $form->createView(),
            'costItem' => $costItem,
        ]);
    }

    public function archive(Request $request, string $id): Response
    {
        $costItemId = CostItemId::fromString($id);
        if ($this->isCsrfTokenValid('archive'.$id, $request->request->get('_token'))) {
            try {
                $this->commandBus->dispatch(new ArchiveCostItem($costItemId));
                $this->addFlash('success', 'Le poste de coût a été archivé.');
            } catch (\DomainException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        } else {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('cost_item_index');
    }
}
