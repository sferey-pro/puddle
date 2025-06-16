<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Twig\Components;

use App\Module\Auth\Application\Command\RegisterUser;
use App\Module\Auth\Application\DTO\RegisterUserDTO;
use App\Module\Auth\UI\Form\RegistrationFormType;
use App\Shared\Application\Command\CommandBusInterface;
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

    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
        $this->data = new RegisterUserDTO();
    }

    protected function instantiateForm(): FormInterface
    {
        // On passe le DTO (formDto) qui sert de modèle de données au formulaire.
        return $this->createForm(RegistrationFormType::class, $this->data);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    #[LiveAction]
    public function save(): ?RedirectResponse
    {
        // Soumet le formulaire avec les données actuelles du composant.
        // La validation est automatiquement déclenchée ici.
        $this->submitForm();

        // Si le formulaire est valide, on procède à la sauvegarde.
        if ($this->getForm()->isValid()) {
            /** @var RegisterUserDTO $dto */
            $dto = $this->getForm()->getData();

            $this->commandBus->dispatch(new RegisterUser($dto));

            $this->addFlash('success', 'Utilisateur enregistré avec succès !');

            return $this->redirectToRoute('login');
        }

        return null;
    }
}
