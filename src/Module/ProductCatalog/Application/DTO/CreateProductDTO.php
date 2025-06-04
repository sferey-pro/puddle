<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;

class CreateProductDTO
{
    #[Sequentially([
        new Assert\NotBlank(message: 'Le nom du produit ne peut pas être vide.'),
        new Assert\Length(max: 100, maxMessage: 'Le nom du produit ne peut excéder 100 caractères.'),
    ])]
    public ?string $name = null;

    /**
     * @var CostComponentLineDTO[]
     */
    #[Assert\Valid()]
    #[Assert\Count(min: 1, minMessage: 'Vous devez ajouter au moins un composant de coût.')]
    public array $costComponents = [];

    public bool $isActive = true;

    public function __construct(
        ?string $name = null,
        array $costComponents = [],
        bool $isActive = true,
    ) {
        $this->name = $name;
        $this->costComponents = $costComponents;
        $this->isActive = $isActive;
    }
}
