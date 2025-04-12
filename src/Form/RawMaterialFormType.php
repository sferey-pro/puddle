<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\RawMaterial;
use App\Entity\Supplier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RawMaterialFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('unitPrice')
            ->add('category', EntityType::class, [
                'placeholder' => false,
                'class' => Category::class,
                'choice_label' => function (Category $category): string {
                    return $category->getDisplayName();
                }
            ])
            ->add('priceVariability')
            ->add('unit')
            ->add('totalCost')
            ->add('notes')
            ->add('link')
            ->add('supplier', EntityType::class, [
                'placeholder' => 'Choose an supplier',
                'class' => Supplier::class,
                'choice_label' => 'name',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RawMaterial::class,
        ]);
    }
}
