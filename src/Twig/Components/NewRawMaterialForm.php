<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Common\Command\CommandBusInterface;
use App\Entity\Category;
use App\Form\RawMaterialFormType;
use App\Messenger\Command\RawMaterial\NewRawMaterial as RawMaterialNewRawMaterial;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class NewRawMaterialForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    #[NotBlank]
    public ?Category $category = null;

    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RawMaterialFormType::class);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    #[ExposeInTemplate]
    public function getCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    #[LiveListener('category:created')]
    public function onCategoryCreated(#[LiveArg] Category $category): void
    {
        $this->category = $category;
    }

    public function isCurrentCategory(Category $category): bool
    {
        return $this->category && $this->category === $category;
    }

    #[LiveAction]
    public function saveRawMaterial(
        CommandBusInterface $commandBus,
    ): Response {
        $this->submitForm();

        $commandBus->dispatch(new RawMaterialNewRawMaterial(
            name: $this->getForm()->getData('name'),
            unitPrice: $this->getForm()->getData('unitPrice'),
            supplier: $this->getForm()->getData('supplier'),
            priceVariability: $this->getForm()->getData('priceVariability'),
            category: $this->category,
            unit: $this->getForm()->getData('unit'),
            totalCost: $this->getForm()->getData('totalCost'),
            notes: $this->getForm()->getData('notes'),
            link: $this->getForm()->getData('link'),
        ));

        $this->addFlash('live_demo_success', 'Product created! Add another one!');

        return $this->redirectToRoute('app_raw_material_index');
    }
}
