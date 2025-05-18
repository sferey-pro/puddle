<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Twig\Components\Paginator;

use App\Shared\Application\Form\ItemsPerPageType;
use App\Shared\Infrastructure\Doctrine\Paginator;
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
        // we can extend AbstractController to get the normal shortcuts
        return $this->createForm(ItemsPerPageType::class);
    }
}
