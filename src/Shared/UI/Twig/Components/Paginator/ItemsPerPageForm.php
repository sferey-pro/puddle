<?php

declare(strict_types=1);

namespace App\Shared\UI\Twig\Components\Paginator;

use App\Core\Infrastructure\Persistence\Paginator\Paginator;
use App\Shared\UI\Form\ItemsPerPageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ItemsPerPageForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true, url: true)]
    public int $limit = Paginator::PAGE_SIZE;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ItemsPerPageType::class);
    }
}
