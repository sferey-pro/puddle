<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Common\Command\CommandBusInterface;
use App\Entity\Category;
use App\Messenger\Command\Product\NewProduct;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class NewProductForm extends AbstractController
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    #[LiveProp(writable: true)]
    #[NotBlank]
    public string $name = '';

    #[LiveProp(writable: true)]
    public float $price = 0;

    #[LiveProp(writable: true)]
    #[NotBlank]
    public ?Category $category = null;

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
    public function saveProduct(
        CommandBusInterface $commandBus,
    ): Response {
        $this->validate();

        $commandBus->dispatch(new NewProduct(
            name: $this->name,
            price: $this->price,
            category: $this->category,
        ));

        $this->addFlash('live_demo_success', 'Product created! Add another one!');

        return $this->redirectToRoute('app_product_index');
    }
}
