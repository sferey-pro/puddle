<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Form;

use App\Module\CostManagement\Application\DTO\AddCostItemDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

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
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner le nom du poste de coût.',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('targetAmount', IntegerType::class, [
                'label' => 'Montant Cible',
                'help' => 'Montant en centimes (par exemple, entrez 1500 pour 15.00€).',
                'attr' => [
                    'min' => 1, // Empêche zéro et négatif au niveau du HTML
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner le montant cible.',
                    ]),
                    new Assert\Positive([
                        'message' => 'Le montant cible doit être un nombre positif.',
                    ]),
                ],
            ])
            ->add('currency', CurrencyType::class, [
                'label' => 'Devise',
                'disabled' => true,
                'preferred_choices' => ['EUR'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une devise.',
                    ]),
                ],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de Début',
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner la date de début.',
                    ]),
                    new Assert\DateTime(),
                ],
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de Fin',
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner la date de fin.',
                    ]),
                    new Assert\DateTime(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddCostItemDTO::class,
        ]);
    }
}
