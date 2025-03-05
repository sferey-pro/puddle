<?php

declare(strict_types=1);

namespace App\Controller;

use App\Common\Command\CommandBusInterface;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Messenger\Command\User\RegisterUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/register', name: 'app_register')]
class RegistrationController extends AbstractController
{
    public function __invoke(
        Request $request,
        CommandBusInterface $commandBus,
    ): Response {
        $form = $this->createForm(RegistrationFormType::class, new User());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandBus->dispatch(new RegisterUser(
                email: $form->get('email')->getData(),
                plainPassword: $form->get('plainPassword')->getData(),
            ));

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
