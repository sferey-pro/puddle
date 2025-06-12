<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Form;

use App\Module\CostManagement\Application\DTO\CreateRecurringCostDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Module\CostManagement\Domain\Enum\RecurrenceFrequency;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RecurringCostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // On imbrique le formulaire de modèle (sans les dates) sur la propriété 'templateData'.
            ->add('template', CostItemTemplateFormType::class, [
                'label' => 'Détails du Coût',
            ])
            ->add('frequency', ChoiceType::class, [
                'label' => 'Fréquence',
                'choices' => array_combine(
                    array_map(fn ($case) => $case->getLabel(), RecurrenceFrequency::cases()),
                    array_map(fn ($case) => $case->value, RecurrenceFrequency::cases())
                ),
            ])
            ->add('day', IntegerType::class, [
                'label' => 'Jour (du mois ou de la semaine)',
                'help' => 'Ex: 15 pour le 15 du mois, ou 1 pour Lundi.'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_token_id' => 'recurring_cost_form_token',
            'data_class' => CreateRecurringCostDTO::class
        ]);
    }
}
