<?php

declare(strict_types=1);

namespace App\Shared\UI\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemsPerPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('items', ChoiceType::class, [
                'choices' => [
                    '10' => 10,
                    '25' => 25,
                    '50' => 50,
                    '100' => 100,
                ],
                'attr' => [
                    'data-model' => 'on(change)|limit',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // configure your form options here
        ]);
    }
}
