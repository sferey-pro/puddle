<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\UI\Twig\Components;

use App\Module\ProductCatalog\Application\DTO\CreateProductDTO;
use App\Module\ProductCatalog\UI\Form\ProductFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
class ProductForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?CreateProductDTO $dto;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(
            ProductFormType::class,
            $this->dto
        );
    }
}
