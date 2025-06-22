<?php

namespace App\Module\Auth\UI\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                'toggle' => true,
                'always_empty' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    // new PasswordStrength(),
                    // new NotCompromisedPassword(),
                ],
                'label' => 'Nouveau mot de passe',
            ])
            ->add('confirmPassword', PasswordType::class, [
                'toggle' => true,
                'always_empty' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez confirmer le mot de passe.']),
                ],
                'label' => 'Confirmer le nouveau mot de passe',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_token_id' => 'reset_password_form_token',
        ]);
    }
}
