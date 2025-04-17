<?php

declare(strict_types=1);

namespace Tests\Unit\Form\Type;

use App\Entity\User;
use App\Form\RegistrationFormType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use Symfony\Component\Form\AbstractExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Validation;

#[CoversClass(RegistrationFormType::class)]
#[UsesClass(User::class)]
class RegistrationFormTypeTest extends TypeTestCase
{
    /**
     * @return list<AbstractExtension>
     */
    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();
        $factory = new class extends ConstraintValidatorFactory {
            public function addValidator(string $className, ConstraintValidatorInterface $validator): void
            {
                $this->validators[$className] = $validator;
            }
        };

        $factory->addValidator('doctrine.orm.validator.unique', $this->createMock(UniqueEntityValidator::class));

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory($factory)
            ->enableAttributeMapping()
            ->getValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    #[Test]
    public function testSubmitValidData(): void
    {
        $formData = [
            'email' => 'acme@example.com',
            'plainPassword' => '123456',
            'agreeTerms' => true,
        ];

        $model = new User();

        $form = $this->factory->create(RegistrationFormType::class, $model);

        $expected = new User();
        $expected->setEmail('acme@example.com');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $this->assertEquals($expected, $model);
    }

    #[Test]
    public function testSubmitInvalidData(): void
    {
        $formData = [
            'email' => 'bad-email',
            'plainPassword' => '1',
            'agreeTerms' => false,
        ];

        $model = new User();

        $form = $this->factory->create(RegistrationFormType::class, $model);

        $expected = new User();
        $expected->setEmail('bad-email');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());

        $this->assertEquals($expected, $model);

        $this->assertSame("ERROR: This value is not a valid email address.\n", (string) $form->get('email')->getErrors());
        $this->assertSame("ERROR: Your password should be at least 6 characters\n", (string) $form->get('plainPassword')->getErrors());
        $this->assertSame("ERROR: You should agree to our terms.\n", (string) $form->get('agreeTerms')->getErrors());
    }
}
