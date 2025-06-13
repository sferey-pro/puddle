<?php

declare(strict_types=1);

namespace App\Module\Sales\UI\Form;

use App\Module\ProductCatalog\Domain\Product;
use App\Module\ProductCatalog\Domain\Repository\ProductRepositoryInterface;
use App\Module\Sales\Application\DTO\OrderLineDTO;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderLineFormType extends AbstractType
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productId', EntityType::class, [
                'class' => Product::class,
                'choice_label' => fn ($product) => $product->getName()->toString(),
                'choice_value' => fn ($product) => $product?->getId()->toString(),
                'label' => 'Produit',
                'placeholder' => 'Sélectionner un produit',
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => ['min' => 1],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderLineDTO::class,
            'csrf_protection' => true,
            'csrf_token_id' => 'order_line_form_token',
        ]);
    }
}
