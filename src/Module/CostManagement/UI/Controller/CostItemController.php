<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Controller;

use App\Module\CostManagement\Application\Command\ArchiveCostItem;
use App\Module\CostManagement\Application\Command\ReactivateCostItem;
use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;
use App\Module\CostManagement\Application\DTO\ReactivateCostItemDTO;
use App\Module\CostManagement\Application\DTO\UpdateCostItemDTO;
use App\Module\CostManagement\Application\Query\FindCostItemQuery;
use App\Module\CostManagement\Application\Query\ListCostItemsQuery;
use App\Module\CostManagement\Domain\Enum\CostItemType;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\UI\Form\CostItemFormType;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CostItemController extends AbstractController
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    #[Template('@CostManagement/index.html.twig')]
    public function index(Request $request): Response
    {
        $costItems = $this->queryBus->ask(new ListCostItemsQuery());

        $groupedCostItems = [
            'unrecognized' => []
        ];

        foreach (CostItemType::cases() as $type) {
            $groupedCostItems[$type->value] = [];
        }

        foreach ($costItems as $costItem) {
            $typeValue = $costItem->type ?: null;

            // Si la clé de type existe dans notre tableau de groupes, c'est un type connu.
            if (isset($groupedCostItems[$typeValue])) {
                $groupedCostItems[$typeValue][] = $costItem;
            } else {
                // Sinon, c'est un type inconnu, on le met dans le groupe "unrecognized".
                $groupedCostItems['unrecognized'][] = $costItem;
            }
        }

        return $this->render('@CostManagement/index.html.twig', [
            'groupedCostItems' => $groupedCostItems,
            'costItemTypes' => CostItemType::cases(),
        ]);
    }

    #[Template('@CostManagement/show.html.twig')]
    public function show(Request $request): array
    {
        $costItem = $this->queryBus->ask(new FindCostItemQuery(
            id: CostItemId::fromString($request->get('id'))
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

    public function archive(Request $request, string $id): Response
    {
        $costItemId = CostItemId::fromString($id);

        if ($this->isCsrfTokenValid('archive_'.$id, $request->request->get('_token'))) {
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

    public function reactivate(Request $request, string $id): Response
    {
        $costItemId = CostItemId::fromString($id);

        if ($this->isCsrfTokenValid('reactivate_'.$id, $request->request->get('_token'))) {
            try {
                $this->commandBus->dispatch(new ReactivateCostItem($costItemId));
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
