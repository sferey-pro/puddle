<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\RawMaterialList;
use App\Form\RawMaterialListFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
class RawMaterialListForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?RawMaterialList $rawMaterialList;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(
            RawMaterialListFormType::class,
            $this->rawMaterialList
        );
    }
}
