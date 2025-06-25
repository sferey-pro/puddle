<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Controller;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Query\QueryBusInterface;
use App\Module\CostManagement\Application\DTO\CreateRecurringCostDTO;
use App\Module\CostManagement\Application\Query\FindRecurringCostQuery;
use App\Module\CostManagement\Application\Query\ListRecurringCostsQuery;
use App\Module\CostManagement\Domain\Exception\RecurringCostException;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class RecurringCostController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly CostItemRepositoryInterface $repository,
    ) {
    }

    /**
     * Gère la création d'un coût récurrent, soit depuis zéro,
     * soit en étant pré-rempli à partir d'un CostItem existant.
     */
    #[Template('@CostManagement/recurring_cost/new.html.twig')]
    public function new(Request $request): array
    {
        $dto = new CreateRecurringCostDTO();
        $fromItemId = $request->get('from_item');

        // Si un ID de CostItem est passé en paramètre, on pré-remplit le DTO
        if ($fromItemId) {
            $modelCostItem = $this->repository->ofId(CostItemId::fromString($fromItemId));

            if ($modelCostItem) {
                $dto->template->name = (string) $modelCostItem->name();
                $dto->template->currency = $modelCostItem->targetAmount()->getCurrency();
                $dto->template->targetAmount = $modelCostItem->targetAmount() ? $modelCostItem->targetAmount()->getAmount() : null;
                $dto->template->type = $modelCostItem->type()->value;
                $dto->template->description = $modelCostItem->description();

                $this->addFlash('info', 'Le formulaire a été pré-rempli à partir du coût sélectionné.');
            }
        }

        // On rend la vue en lui passant le DTO, qu'il soit vide ou pré-rempli.
        return [
            'formDto' => $dto,
        ];
    }

    #[Template('@CostManagement/recurring_cost/index.html.twig')]
    public function index(): array
    {
        $recurringCosts = $this->queryBus->ask(new ListRecurringCostsQuery());

        return [
            'recurringCosts' => $recurringCosts,
        ];
    }

    #[Template('@CostManagement/recurring_cost/show.html.twig')]
    public function show(Request $request): array
    {
        $id = RecurringCostId::fromString($request->get('id'));
        $recurringCost = $this->queryBus->ask(new FindRecurringCostQuery($id));

        if (!$recurringCost) {
            throw RecurringCostException::notFoundWithId($id);
        }

        return [
            'recurringCost' => $recurringCost,
            'generated_items' => [],
        ];
    }
}
