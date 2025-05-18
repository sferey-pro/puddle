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

class RegistrationController extends AbstractController
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
    ) {
    }

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

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
