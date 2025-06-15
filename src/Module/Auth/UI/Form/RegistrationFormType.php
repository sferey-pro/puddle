<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Form;

use App\Module\Auth\Application\DTO\RegisterUserDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class, [
                'toggle' => true,
                'always_empty' => false,
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add('agreeTerms', CheckboxType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegisterUserDTO::class,
            'csrf_protection' => true,
            'csrf_token_id' => 'registration_form_token',
        ]);
    }
}
