<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Twig\Components;

use App\Module\CostManagement\Application\Command\RemoveCostContribution;
use App\Module\CostManagement\Application\Query\FindCostItemQuery;
use App\Module\CostManagement\Application\ReadModel\CostItemView;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * Affiche la liste des contributions pour un poste de coût
 * et gère leur suppression de manière dynamique.
 */
#[AsLiveComponent]
final class CostContributionList
{
    use DefaultActionTrait;

    #[LiveProp(updateFromParent: true)]
    public string $costItemId;

    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    /**
     * Retourne la vue du poste de coût avec ses contributions.
     */
    public function getCostItem(): CostItemView
    {
        return $this->queryBus->ask(new FindCostItemQuery(CostItemId::fromString($this->costItemId)));
    }

    #[LiveAction]
    public function remove(#[LiveArg()] string $contributionId): void
    {
        $this->commandBus->dispatch(
            new RemoveCostContribution($this->costItemId, $contributionId)
        );
    }
}
