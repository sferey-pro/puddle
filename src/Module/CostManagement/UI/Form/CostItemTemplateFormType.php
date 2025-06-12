<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Form;

use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire dédié à la création d'un "CostItem" qui servira de modèle (template).
 * Il ne contient que les champs nécessaires à la configuration d'un coût,
 * et ignore volontairement la période de couverture.
 */
class CostItemTemplateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('startDate')
            ->remove('endDate');
    }

    public function getParent(): string
    {
        return CostItemFormType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateCostItemDTO::class,
            'csrf_protection' => true,
            'csrf_token_id' => 'cost_item_form_token',
        ]);
    }
}
