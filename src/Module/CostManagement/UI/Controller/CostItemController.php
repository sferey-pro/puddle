<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Controller;

use App\Module\CostManagement\Application\Command\ArchiveCostItem;
use App\Module\CostManagement\Application\Command\ReactivateCostItem;
use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;
use App\Module\CostManagement\Application\Query\FindCostItemInstanceQuery;
use App\Module\CostManagement\Application\Query\FindCostItemTemplateQuery;
use App\Module\CostManagement\Application\Query\ListCostItemsQuery;
use App\Module\CostManagement\Domain\Enum\CostItemType;
use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class CostItemController extends AbstractController
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    #[Template('@CostManagement/cost_item/index.html.twig')]
    public function index(): array
    {
        $costItems = $this->queryBus->ask(new ListCostItemsQuery());

        $groupedCostItems = [
            'unrecognized' => [],
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

        return [
            'groupedCostItems' => $groupedCostItems,
            'costItemTypes' => CostItemType::cases(),
        ];
    }

    #[Template('@CostManagement/cost_item/show.html.twig')]
    public function show(Request $request): array
    {
        $id = CostItemId::fromString($request->get('id'));
        // Essai n°1 : Est-ce une instance ?
        $instanceView = $this->queryBus->ask(new FindCostItemInstanceQuery($id));
        if ($instanceView) {
            return [
                'costItem' => $instanceView,
            ];
        }

        // Essai n°2 : Si ce n'est pas une instance, est-ce un modèle ?
        $templateView = $this->queryBus->ask(new FindCostItemTemplateQuery($id));
        if ($templateView) {
            return [
                'costItem' => $templateView,
            ];
        }

        throw CostItemException::notFoundWithId($id);
    }

    #[Template('@CostManagement/cost_item/new.html.twig')]
    public function new(): array
    {
        return [
            'formDto' => new CreateCostItemDTO(),
        ];
    }

    public function archive(Request $request, string $id): RedirectResponse
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

    public function reactivate(Request $request, string $id): RedirectResponse
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
