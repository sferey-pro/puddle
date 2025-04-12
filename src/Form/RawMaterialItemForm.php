<?php

namespace App\Form;

use App\Entity\RawMaterial;
use App\Entity\RawMaterialItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RawMaterialItemForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raw_material', EntityType::class, [
                'class' => RawMaterial::class,
                'choice_label' => 'name',
            ])
            ->add('quantity')
            ->add('unit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => RawMaterialItem::class]);
    }
}
