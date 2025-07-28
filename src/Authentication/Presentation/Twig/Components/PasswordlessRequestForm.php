<?php

declare(strict_types=1);

namespace Authentication\Presentation\Twig\Components;

use Authentication\Application\DTO\PasswordlessDTO;
use Authentication\Application\Service\IdentifierResolverInterface;
use Authentication\Application\Service\PasswordlessAuthenticationService;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Presentation\Form\PasswordlessFormType;
use Kernel\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
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

    #[LiveProp()]
    public ?PasswordlessDTO $data;

    #[LiveProp()]
    public ?string $errorMessage = null;

    public function __construct(
        private CommandBusInterface $commandBus,
        private PasswordlessAuthenticationService $authService,
        private LoggerInterface $logger
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

        if ($this->getForm()->isValid()) {

            try {

                $type = $this->authService->initiatePasswordlessAuthentication(
                    identifier: $this->data->identifier,
                    ipAddress: $request->getClientIp(),
                    userAgent: $request->headers->get('User-Agent')
                );

                switch ($type) {
                    case 'magic_link': // Email envoyé
                        $this->addFlash('success', 'Check your email! We sent you a magic link.');
                        return $this->redirectToRoute('passwordless_email_sent', [
                            'email' => $this->data->identifier
                        ]);
                        break;
                    case 'otp': // SMS envoyé - redirection vers saisie OTP
                        $this->addFlash('info', 'We sent you a verification code by SMS.');
                        return $this->redirectToRoute('passwordless_verify_otp', [
                            'phone' => base64_encode($this->data->identifier)
                        ]);
                        break;
                    default:
                        return $this->redirectToRoute('passwordless_error');
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
        }

        return null;
    }
}
