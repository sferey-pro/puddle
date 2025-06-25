<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Command;

use App\Core\Application\Command\CommandBusInterface;
use App\Module\Auth\Application\Command\CleanupPasswordRequests;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Fournit une interface en ligne de commande (CLI) pour la tâche de maintenance du nettoyage des demandes.
 *
 * Le rôle de cette classe, en tant qu'adaptateur, est de traduire une commande exécutée
 * par un administrateur ou un script cron en une Commande CQRS compréhensible par
 * le cœur de l'application. Elle gère uniquement l'interaction avec la console.
 */
#[AsCommand(
    name: 'puddle:auth:cleanup-reset-requests',
    description: 'Nettoie les anciennes demandes de réinitialisation de mot de passe expirées.',
)]
final class CleanupPasswordRequestsCommand extends Command
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    /**
     * Définit les options de la commande, comme le seuil en jours pour le nettoyage.
     */
    protected function configure(): void
    {
        $this
            ->addOption(
                'days-old',
                null,
                InputOption::VALUE_REQUIRED,
                'Supprime les demandes expirées depuis plus de X jours.',
                7 // Valeur métier par défaut : on garde une semaine d'historique.
            );
    }

    /**
     * Exécute la logique de la commande : dispatcher la commande de nettoyage et afficher le résultat.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $daysOld = (int) $input->getOption('days-old');

        $io->title('Nettoyage des anciennes demandes de réinitialisation de mot de passe');

        // On délègue le travail au Handler via le bus de commande et on récupère le résultat.
        $deletedCount = $this->commandBus->dispatch(new CleanupPasswordRequests($daysOld));

        if ($deletedCount > 0) {
            $io->success("{$deletedCount} demande(s) ancienne(s) ont été supprimée(s).");
        } else {
            $io->info('Aucune demande à supprimer.');
        }

        return Command::SUCCESS;
    }
}
