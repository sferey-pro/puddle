<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Controller;

use App\Module\CostManagement\Application\DTO\AddCostItemDTO;
use App\Module\CostManagement\Application\Query\FindCostItemQuery;
use App\Module\CostManagement\Application\Query\ListCostItemsQuery;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\Doctrine\Paginator;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class CostItemController extends AbstractController
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/', name: 'index')]
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
        $product = $this->queryBus->ask(new FindCostItemQuery(
            identifier: CostItemId::fromString($request->get('id'))
        ));

        return [
            'costItem' => $product,
        ];
    }

    #[Template('@CostManagement/new.html.twig')]
    public function new(): array
    {
        return [
            'formDto' => new AddCostItemDTO(),
        ];
    }
}
