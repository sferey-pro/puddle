<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Symfony\Command;

use App\Module\CostManagement\Application\ReadModel\CostItemView;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemViewRepositoryInterface;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Commande pour réconcilier les Read Models des CostItems (MongoDB)
 * avec les agrégats du Write Model (PostgreSQL).
 */
#[AsCommand(
    name: 'puddle:cost-items:reconcile',
    description: 'Synchronise les vues des postes de coût avec le modèle de domaine.',
)]
class ReconcileCostItemsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CostItemRepositoryInterface $costItemRepository,
        private readonly CostItemViewRepositoryInterface $costItemViewRepository,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Début de la réconciliation des Postes de Coût');

        $reconciledCount = 0;
        $createdCount = 0;

        // On itère sur tous les agrégats du Write Model pour éviter de tout charger en mémoire
        $costItemIterable = $this->costItemRepository->findAll();

        foreach ($costItemIterable as $costItem) {
            $costItemView = $this->costItemViewRepository->findById($costItem->id());

            if (!$costItemView) {
                // Le Read Model n'existe pas, il faut le créer
                $this->createViewFromAggregate($costItem);
                $this->logger->warning('CostItemView manquant créé.', ['id' => (string)$costItem->id()]);
                $createdCount++;
            } elseif ($costItemView->isDifferentFrom($costItem)) {
                // Le Read Model existe mais est désynchronisé, on le met à jour
                $costItemView->updateFromAggregate($costItem);
                $this->costItemViewRepository->save($costItemView);
                $this->logger->info('CostItemView réconcilié.', ['id' => (string)$costItem->id()]);
                $reconciledCount++;
            }

            // On détache l'entité du manager pour libérer la mémoire
            $this->entityManager->detach($costItem);
        }

        $io->success(sprintf(
            'Réconciliation terminée. %d vues créées, %d vues mises à jour.',
            $createdCount,
            $reconciledCount
        ));

        return Command::SUCCESS;
    }

    private function createViewFromAggregate(CostItem $costItem): void
    {
        $costItemView = CostItemView::fromAggregate($costItem);
        $this->costItemViewRepository->save($costItemView);
    }
}
