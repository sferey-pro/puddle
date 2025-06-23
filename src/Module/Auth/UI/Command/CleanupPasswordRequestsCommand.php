<?php

namespace App\Module\Auth\UI\Command;

use App\Module\Auth\Application\Command\CleanupPasswordRequests;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'puddle:auth:cleanup-reset-requests',
    description: 'Nettoie les anciennes demandes de réinitialisation de mot de passe expirées.',
)]
final class CleanupPasswordRequestsCommand extends Command
{
    public function __construct(
        private readonly CommandBusInterface $commandBus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'days-old',
                null,
                InputOption::VALUE_REQUIRED,
                'Supprime les demandes expirées depuis plus de X jours.',
                7 // Valeur par défaut : 1 semaine
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $daysOld = (int) $input->getOption('days-old');

        $io->title('Nettoyage des anciennes demandes de réinitialisation de mot de passe');

        // 1. On crée notre commande CQRS avec l'option de la CLI
        $cleanupCommand = new CleanupPasswordRequests($daysOld);

        // 2. On la dispatche et on récupère le résultat du handler
        $deletedCount = $this->commandBus->dispatch($cleanupCommand);

        // 3. On affiche le résultat
        if ($deletedCount > 0) {
            $io->success("{$deletedCount} demande(s) ancienne(s) ont été supprimée(s).");
        } else {
            $io->info('Aucune demande à supprimer.');
        }

        return Command::SUCCESS;
    }
}
