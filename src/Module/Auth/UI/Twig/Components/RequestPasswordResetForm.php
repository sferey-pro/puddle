<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Twig\Components;

use App\Module\Auth\Application\Command\RequestPasswordReset;
use App\Module\Auth\Domain\Exception\PasswordResetException;
use App\Module\Auth\UI\Form\RequestPasswordResetFormType;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\UI\Twig\Extension\HumanReadableDurationExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * Gère le formulaire interactif pour la première étape de la réinitialisation de mot de passe.
 *
 * En tant que LiveComponent, il est responsable de l'état du formulaire, de la gestion
 * des entrées utilisateur, et de la communication avec la couche Application pour
 * initier le processus métier. Il gère également l'affichage des erreurs spécifiques,
 * comme le blocage dû à un trop grand nombre de tentatives.
 */
#[AsLiveComponent]
final class RequestPasswordResetForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp()]
    public ?string $throttleMessage = null;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly RequestStack $requestStack,
        private readonly HumanReadableDurationExtension $durationExtension,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RequestPasswordResetFormType::class);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    /**
     * Action déclenchée lorsque l'utilisateur soumet le formulaire.
     */
    #[LiveAction]
    public function save(): ?RedirectResponse
    {
        $this->submitForm();

        if ($this->getForm()->isValid()) {
            $email = $this->getForm()->getData()['email'];
            $ip = $this->requestStack->getCurrentRequest()?->getClientIp() ?? '127.0.0.1';

            try {
                $expireAt = $this->commandBus->dispatch(new RequestPasswordReset($email, $ip));

                return $this->redirectToRoute('forgot_password_check_email', ['expiresAt' => $expireAt]);
            } catch (PasswordResetException $e) {
                // En cas de blocage, on informe l'utilisateur du temps d'attente restant.
                $remainingTime = $this->durationExtension->formatHumanDuration($e->payload('availableAt'));
                $this->throttleMessage = "Vous avez fait trop de demandes. Veuillez réessayer dans {$remainingTime}.";
            }
        }

        return null;
    }
}
