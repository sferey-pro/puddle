<?php

declare(strict_types=1);

namespace Authentication\Presentation\Twig\Components;

use Authentication\Application\DTO\PasswordlessDTO;
use Authentication\Application\Service\PasswordlessAuthenticationService;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Presentation\Form\PasswordlessFormType;
use Kernel\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use SharedKernel\Domain\Service\IdentifierAnalyzerInterface;
use SharedKernel\Domain\Service\IdentityContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PasswordlessRequestForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?PasswordlessDTO $data;

    #[LiveProp]
    public ?string $errorMessage = null;

    #[LiveProp]
    public ?string $identifierType = null;

    #[LiveProp]
    public ?string $maskedIdentifier = null;

    #[LiveProp]
    public ?string $displayMessage = null;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly PasswordlessAuthenticationService $authService,
        private readonly IdentifierAnalyzerInterface $identifierAnalyzer,
        private readonly IdentityContextInterface $identityContext,
        private readonly LoggerInterface $logger
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        $this->data ??= new PasswordlessDTO();
        return $this->createForm(PasswordlessFormType::class, $this->data);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    #[LiveAction]
    public function save(Request $request): ?RedirectResponse
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            return null;
        }

        $analysis = $this->identifierAnalyzer->analyze($this->data->identifier);

        if (!$analysis->isValid) {
            $this->errorMessage = $analysis->errorMessage;
            return null;
        }

        try {

            $identifierResult = $this->identityContext->resolveIdentifier($analysis->normalizedValue);

            if ($identifierResult->isFailure()) {
                $this->errorMessage = 'Invalid identifier format.';
                return null;
            }

            $this->authService->initiatePasswordlessAuthentication(
                identifier: $identifierResult->value,
                ipAddress: $request->getClientIp(),
                userAgent: $request->headers->get('User-Agent')
            );


            if ($analysis->isEmail()) {
                $this->addFlash('success', 'Check your email! We sent you a magic link.');
                return $this->redirectToRoute('passwordless_email_sent', [
                    'email' => $analysis->maskedValue
                ]);
            } else {
                $this->addFlash('info', 'We sent you a verification code by SMS.');
                return $this->redirectToRoute('passwordless_verify_otp', [
                    'phone' => base64_encode($analysis->normalizedValue)
                ]);
            }

        } catch (TooManyAttemptsException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred. Please try again.');
            $this->logger->error('Passwordless request failed', [
                'error' => $e->getMessage(),
                'identifier' => $this->data->identifier
            ]);
        }
        return null;
    }

    private function resetAnalysis(): void
    {
        $this->identifierType = null;
        $this->maskedIdentifier = null;
        $this->displayMessage = null;
    }
}
