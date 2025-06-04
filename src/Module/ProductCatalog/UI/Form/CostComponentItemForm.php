<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\UI\Form;

use App\Module\ProductCatalog\Application\DTO\CostComponentLineDTO;
use App\Module\ProductCatalog\Domain\ValueObject\CostComponentType;
use App\Module\ProductCatalog\Domain\ValueObject\UnitOfMeasure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CostComponentItemForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du Composant',
                'attr' => ['placeholder' => 'Ex: Grain de café, Loyer'],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => array_combine(
                    array_map(fn ($case) => $case->getLabel(), CostComponentType::cases()),
                    array_map(fn ($case) => $case->value, CostComponentType::cases())
                ),
                'placeholder' => 'Sélectionnez un type',
            ])
            ->add('costAmount', NumberType::class, [
                'label' => 'Coût (€)', // Assumant EUR
                'scale' => 2, // Pour les centimes
                'html5' => true,
                'attr' => ['step' => '0.01'],
            ])
            ->add('costCurrency', ChoiceType::class, [
                'label' => 'Devise',
                'choices' => [
                    'Euro (€)' => 'EUR',
                ],
                'disabled' => true,
            ])
            ->add('quantityValue', NumberType::class, [
                'label' => 'Quantité (Optionnel)',
                'required' => false,
                'scale' => 3, // Permet plus de précision pour les grammes/ml
                'html5' => true,
                'attr' => ['step' => 'any'],
            ])
            ->add('quantityUnit', ChoiceType::class, [
                'label' => 'Unité (Optionnel)',
                'choices' => array_combine(
                    array_map(fn ($case) => $case->getLabel(), UnitOfMeasure::cases()),
                    array_map(fn ($case) => $case->value, UnitOfMeasure::cases())
                ),
                'placeholder' => 'Sélectionnez une unité',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CostComponentLineDTO::class,
        ]);
    }
}
