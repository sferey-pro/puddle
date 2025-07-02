<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Twig\Components;

use App\Core\Application\Command\CommandBusInterface;
use App\Module\Auth\Application\Command\StartRegistrationSaga;
use App\Module\Auth\Application\DTO\RegisterUserDTO;
use App\Module\Auth\Domain\Exception\UserException;
use App\Module\Auth\UI\Form\RegistrationFormType;
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
        $this->data = new RegisterUserDTO();
    }

    protected function instantiateForm(): FormInterface
    {
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
            /** @var RegisterUserDTO $dto */
            $dto = $this->getForm()->getData();

            try {
                $this->commandBus->dispatch(new StartRegistrationSaga($dto));
                $this->addFlash('success', 'Votre compte a été créé. Veuillez vérifier votre boîte de réception pour valider votre e-mail.');

                return $this->redirectToRoute('login');
            } catch (UserException $e) {
                $this->errorMessage = $e->getMessage();
            }
        }

        return null;
    }
}
