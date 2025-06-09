<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Form;

use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;
use App\Module\CostManagement\Domain\Enum\CostItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CostItemFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du poste de coût',
                'attr' => [
                    'placeholder' => 'Ex: Loyer Janvier, Facture électricité',
                ],
                'empty_data' => '',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type ',
                'choices' => array_combine(
                    array_map(fn ($case) => $case->getLabel(), CostItemType::cases()),
                    array_map(fn ($case) => $case->value, CostItemType::cases())
                ),
                'placeholder' => 'Sélectionnez un type',
            ])
            ->add('targetAmount', MoneyType::class, [
                'label' => 'Montant Cible',
                'divisor' => 100,
                'currency' => false,
            ])
            ->add('currency', ChoiceType::class, [
                'label' => 'Devise',
                'disabled' => true,
                'choices' => [
                    'Euro (€)' => 'EUR',
                ],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de Début',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de Fin',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (Optionnel)',
                'required' => false,
            ])
        ;
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
