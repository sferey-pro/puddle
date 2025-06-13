<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\UI\Form;

use App\Module\ProductCatalog\Application\DTO\CreateProductDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du Produit',
                'attr' => ['placeholder' => 'Ex: Espresso, Cappuccino'],
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
            ])
            ->add('costComponents', LiveCollectionType::class, [
                'entry_type' => CostComponentItemForm::class,
                'entry_options' => ['label' => false],
                'label' => 'Composants du Coût',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateProductDTO::class,
            'csrf_protection' => true,
            'csrf_token_id' => 'product_form_token',
        ]);
    }
}
