<?php

declare(strict_types=1);

namespace App\Controller;

use App\Common\Query\QueryBusInterface;
use App\Doctrine\Paginator;
use App\Entity\User;
use App\Entity\ValueObject\UserId;
use App\Form\UserType;
use App\Messenger\Query\User\FindUserQuery;
use App\Messenger\Query\User\FindUsersQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/user')]
final class UserController extends AbstractController
{
    public function __construct(
        private QueryBusInterface $queryBus
    ) {
    }

    #[Route('/', name: 'user_index', defaults: ['page' => '1', '_format' => 'html'], methods: ['GET'])]
    #[Route('/page/{page}', name: 'user_index_paginated', defaults: ['_format' => 'html'], requirements: ['page' => Requirement::POSITIVE_INT], methods: ['GET'])]
    public function index(
        int $page,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $limit = Paginator::PAGE_SIZE,
    ): Response {
        $models = $this->queryBus->ask(new FindUsersQuery($page, $limit));

        return $this->render('user/index.html.twig', [
            'users' => $models,
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User(
            identifier: new UserId()
        );

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword("Not password");

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(
        #[ValueResolver('user_id')]
        UserId $id
    ): Response {

        $model = $this->queryBus->ask(new FindUserQuery(identifier: $id));

        return $this->render('user/show.html.twig', [
            'user' => $model,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[ValueResolver('user_id')]
        UserId $id,
        EntityManagerInterface $entityManager
    ): Response {

        $user = $this->queryBus->ask(new FindUserQuery(identifier: $id));

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
