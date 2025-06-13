<?php

declare(strict_types=1);

namespace App\Module\Sales\UI\Form;

use App\Module\Sales\Application\DTO\CreateOrderDTO;
use App\Module\UserManagement\Domain\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name.fullName',
                'choice_value' => fn($user) => $user?->getId()->toString(),
                'label' => 'Client',
                'placeholder' => 'Sélectionner un client',
            ])
            ->add('orderLines', CollectionType::class, [
                'entry_type' => OrderLineFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Lignes de commande',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer la commande',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateOrderDTO::class,
            'csrf_protection' => true,
            'csrf_token_id' => 'order_form_token',
        ]);
    }
}
