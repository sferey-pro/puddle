<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\DTO;

use App\Module\ProductCatalog\Domain\Enum\CostComponentType;
use App\Module\ProductCatalog\Domain\Enum\UnitOfMeasure;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;

class CostComponentLineDTO
{
    #[Sequentially([
        new Assert\NotBlank(message: 'Le nom du composant ne peut pas être vide.'),
        new Assert\Length(max: 150, maxMessage: 'Le nom du composant ne peut excéder 150 caractères.'),
    ])]
    public ?string $name = null;

    #[Sequentially([
        new Assert\NotBlank(message: 'Le type du composant doit être sélectionné.'),
        new Assert\Choice(callback: [CostComponentType::class, 'values'], message: 'Type de composant invalide.'),
    ])]
    public ?string $type = null; // Stocke la valeur du string de l'enum CostComponentType

    #[Sequentially([
        new Assert\NotBlank(message: 'Le coût ne peut pas être vide.'),
        new Assert\Type(type: 'numeric', message: 'Le coût doit être un nombre.'),
        new Assert\PositiveOrZero(message: 'Le coût doit être positif ou nul.'),
    ])]
    public ?float $costAmount = null;

    // La devise pourrait être fixée globalement ou sélectionnable si besoin. Pour l'instant, on assume EUR.
    #[Sequentially([
        new Assert\NotBlank(),
        new Assert\Currency(),
    ])]
    public string $costCurrency = 'EUR';

    #[Sequentially([
        new Assert\Type(type: 'numeric', message: 'La quantité doit être un nombre.'),
        new Assert\PositiveOrZero(message: 'La quantité doit être positive ou nulle.'),
    ])]
    public ?float $quantityValue = null;

    #[Sequentially([
        new Assert\Choice(callback: [UnitOfMeasure::class, 'values'], message: 'Unité de mesure invalide.', groups: ['RawMaterialChecks']),
    ])]
    public ?string $quantityUnit = null; // Stocke la valeur du string de l'enum UnitOfMeasure

    // Constructeur optionnel pour initialiser avec des valeurs par défaut si nécessaire
    public function __construct(
        ?string $name = null,
        ?string $type = null,
        ?float $costAmount = null,
        string $costCurrency = 'EUR',
        ?float $quantityValue = null,
        ?string $quantityUnit = null,
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->costAmount = $costAmount;
        $this->costCurrency = $costCurrency;
        $this->quantityValue = $quantityValue;
        $this->quantityUnit = $quantityUnit;
    }
}
