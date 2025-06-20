<?php

declare(strict_types=1);

namespace App\Module\UserManagement\UI\Form;

use App\Module\UserManagement\Application\DTO\CreateUserDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Username',
                'required' => false,
            ])
        ;

        if ($options['is_creation']) {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'Email',
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateUserDTO::class,
            'is_creation' => true,
        ]);
    }
}
