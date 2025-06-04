<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Form;

use App\Module\CostManagement\Application\DTO\AddContributionDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddContributionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class, [
                'label' => 'Montant de la contribution',
                'html5' => true,
                'attr' => [
                    'step' => '0.01',
                    'placeholder' => 'ex: 50.25',
                ],
            ])
            ->add('costItemId', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddContributionDTO::class,
        ]);
    }
}
