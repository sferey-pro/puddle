<?php

namespace Authentication\Presentation\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulaire de vérification OTP
 */
class OTPVerificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Verification Code',
                'attr' => [
                    'placeholder' => '000000',
                    'class' => 'form-control form-control-lg text-center',
                    'maxlength' => 6,
                    'pattern' => '[0-9]{6}',
                    'inputmode' => 'numeric',
                    'autocomplete' => 'one-time-code',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^\d{6}$/',
                        'message' => 'Code must be 6 digits'
                    ]),
                ],
            ])
            ->add('phone', HiddenType::class);
    }
}
