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

    #[LiveAction]
    public function save(): ?RedirectResponse
    {
        $this->submitForm();

        if ($this->getForm()->isValid()) {
            $email = $this->getForm()->getData()['email'];
            $ip = $this->requestStack->getCurrentRequest()?->getClientIp() ?? '127.0.0.1';

            try {
                $this->commandBus->dispatch(new RequestPasswordReset($email, $ip));

                return $this->redirectToRoute('forgot_password_check_email');
            } catch (PasswordResetException $e) {
                $remainingTime = $this->durationExtension->formatHumanDuration($e->payload('availableAt'));
                $this->throttleMessage = "Vous avez fait trop de demandes. Veuillez réessayer dans {$remainingTime}.";
            }
        }

        return null;
    }
}
