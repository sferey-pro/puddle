<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller;

use App\Module\Auth\Application\Command\Register\RegisterUser;
use App\Module\Auth\Application\DTO\RegisterUserDTO;
use App\Module\Auth\UI\Form\RegistrationFormType;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur gérant le processus d'inscription des nouveaux utilisateurs.
 *
 * Ce contrôleur est responsable de l'affichage et du traitement du formulaire d'inscription.
 * En cas de soumission valide du formulaire, il délègue la création de l'utilisateur
 * à la couche applicative via une commande.
 */
class RegistrationController extends AbstractController
{
    /**
     * @param CommandBusInterface $commandBus le bus de commandes pour dispatcher la commande d'inscription
     */
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
    ) {
    }

    /**
     * Gère la requête d'inscription.
     *
     * Crée et traite le formulaire d'inscription. Si le formulaire est soumis et valide,
     * une commande RegisterUser est envoyée au bus de commandes et l'utilisateur est redirigé.
     * Sinon, le template du formulaire d'inscription est rendu.
     *
     * @param Request $request la requête HTTP entrante
     *
     * @return Response la réponse HTTP (redirection ou rendu du template)
     */
    public function __invoke(
        Request $request,
    ): Response {
        $form = $this->createForm(RegistrationFormType::class, new RegisterUserDTO());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->dispatch(new RegisterUser(
                dto: new RegisterUserDTO(
                    email: $form->get('email')->getData(),
                    plainPassword: $form->get('plainPassword')->getData(),
                    agreeTerms: $form->get('agreeTerms')->getData()
                )
            ));

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@Auth/registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
