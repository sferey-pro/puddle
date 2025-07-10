<?php

declare(strict_types=1);

namespace Account\Registration\Presentation\Twig\Components;

use Account\Registration\Application\Command\StartRegistrationSaga;
use Account\Registration\Application\DTO\RegisterUserDTO;
use Account\Registration\Domain\Exception\RegistrationException;
use Account\Registration\Presentation\Form\RegistrationFormType;
use Kernel\Application\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class RegistrationForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp()]
    public ?RegisterUserDTO $data;

    #[LiveProp()]
    public ?string $errorMessage = null;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        $this->data ??= new RegisterUserDTO();
        return $this->createForm(RegistrationFormType::class, $this->data);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    #[LiveAction]
    public function save(): ?RedirectResponse
    {
        $this->submitForm();

        if ($this->getForm()->isValid()) {
            try {
                $command = new StartRegistrationSaga(
                    identifier: $this->data->identifier
                );

                $this->commandBus->dispatch($command);

                $this->addFlash('success', 'Votre compte est en cours de création.');

                return $this->redirectToRoute('login');
            } catch (RegistrationException $e) {
                $this->errorMessage = $e->getMessage();
            }
        }

        return null;
    }
}
