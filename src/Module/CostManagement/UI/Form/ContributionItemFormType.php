<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Form;

use App\Module\CostManagement\Application\DTO\AddContributionDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContributionItemFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class, [
                'label' => 'Montant',
                'html5' => true,
                'scale' => 2,
                'attr' => [
                    'step' => '0.01',
                    'placeholder' => 'ex: 50.25',
                ],
            ])
            ->add('sourceProductId', TextType::class, [
                'label' => 'ID Produit Source (Optionnel)',
                'required' => false,
                'attr' => ['placeholder' => 'ID du produit...'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddContributionDTO::class,
            'csrf_protection' => true,
            'csrf_token_id' => 'contributution_item_form_token',
        ]);
    }
}
