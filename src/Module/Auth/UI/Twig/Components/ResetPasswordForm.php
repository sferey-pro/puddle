<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Twig\Components;

use App\Core\Application\Command\CommandBusInterface;
use App\Module\Auth\Application\Command\ResetPassword;
use App\Module\Auth\Domain\Exception\PasswordResetException;
use App\Module\Auth\UI\Form\ResetPasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * Gère le formulaire interactif pour la dernière étape de la réinitialisation de mot de passe.
 *
 * Ce composant permet à un utilisateur disposant d'un token valide de définir son nouveau
 * mot de passe. Il est responsable de la validation des champs, de la comparaison des mots de passe
 * et de la communication avec la couche Application pour finaliser le processus.
 */
#[AsLiveComponent]
final class ResetPasswordForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public string $token;

    #[LiveProp]
    public ?string $domainError = null;

    #[LiveProp]
    public ?string $passwordMismatchError = null;

    public function __construct(private readonly CommandBusInterface $commandBus)
    {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ResetPasswordFormType::class);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    /**
     * Action déclenchée lorsque l'utilisateur soumet son nouveau mot de passe.
     *
     * Elle valide que les mots de passe correspondent, puis dispatche la commande de
     * réinitialisation. Elle intercepte les erreurs métier (ex: token invalide) pour
     * les afficher à l'utilisateur.
     */
    #[LiveAction]
    public function save(): ?RedirectResponse
    {
        $this->passwordMismatchError = null;
        $this->domainError = null;

        $this->submitForm();
        $form = $this->getForm();
        $data = $form->getData();

        if ($form->isValid()) {
            if ($data['plainPassword'] !== $data['confirmPassword']) {
                $this->passwordMismatchError = 'Les mots de passe ne correspondent pas.';

                return null;
            }

            try {
                $password = $data['plainPassword'];
                $this->commandBus->dispatch(new ResetPassword($this->token, $password));

                $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');

                return $this->redirectToRoute('login');
            } catch (PasswordResetException $e) {
                $this->domainError = $e->getMessage();
            } catch (\Throwable $e) {
                $this->domainError = 'Une erreur inattendue est survenue.';
            }
        }

        return null;
    }
}
