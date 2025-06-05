<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Tests\Story;

use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Tests\Factory\CostItemFactory;
use Zenstruck\Foundry\Story;
use function Zenstruck\Foundry\faker;

final class CostItemStory extends Story
{
    // Constantes pour des noms réutilisables ou identifiables dans les tests
    public const ITEM_NAME_ACTIVE_CURRENT_RENT = 'Loyer Bureau Actuel';
    public const ITEM_NAME_COVERED_INTERNET = 'Facture Internet Couverte (Mois Dernier)';
    public const ITEM_NAME_ARCHIVED_OLD_SUPPLIES = 'Fournitures Anciennes Archivées';
    public const ITEM_NAME_FUTURE_SOFTWARE_LICENSE = 'Licence Logiciel (Mois Prochain)';
    public const ITEM_NAME_ACTIVE_MARKETING_CAMPAIGN = 'Campagne Marketing en Cours';

    public function build(): void
    {
        // Un poste de coût actif pour le mois courant
        CostItemFactory::new()->forCurrentMonth()->create([
            'name' => new CostItemName(self::ITEM_NAME_ACTIVE_CURRENT_RENT),
            'targetAmount' => faker()->money(150.000, 250.000, 'EUR'), // Montant en centimes
        ]);

        // Un poste de coût couvert du mois précédent
        CostItemFactory::new()->forPreviousMonth()->covered()->create([
            'name' => new CostItemName(self::ITEM_NAME_COVERED_INTERNET),
            'targetAmount' => faker()->money(5.000, 10.000, 'EUR'),
        ]);

        // Un poste de coût archivé
        CostItemFactory::new()->archived()->forPreviousMonth()->create([
            'name' => new CostItemName(self::ITEM_NAME_ARCHIVED_OLD_SUPPLIES),
        ]);

        // Un poste de coût pour le mois prochain
        CostItemFactory::new()->forNextMonth()->create([
            'name' => new CostItemName(self::ITEM_NAME_FUTURE_SOFTWARE_LICENSE),
            'targetAmount' => faker()->money(20.000, 60.000, 'EUR'),
        ]);

        // Un autre poste actif avec une couverture partielle
        CostItemFactory::new()->forCurrentMonth()->withSpecificAmountCovered(30000)->create([
            'name' => new CostItemName(self::ITEM_NAME_ACTIVE_MARKETING_CAMPAIGN),
            'targetAmount' => faker()->money(100.000, 150.000, 'EUR'),
        ]);

        // Quelques postes de coûts divers générés aléatoirement
        CostItemFactory::createMany(5);
    }
}
