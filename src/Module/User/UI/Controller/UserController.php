<?php

declare(strict_types=1);

namespace App\Module\User\UI\Controller;

use App\Module\User\Application\Command\CreateUser;
use App\Module\User\Application\DTO\CreateUserDTO;
use App\Module\User\Application\Query\FindUsersQuery;
use App\Module\User\UI\Form\UserFormType;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\Doctrine\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

final class UserController extends AbstractController
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function index(
        int $page,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $limit = Paginator::PAGE_SIZE,
    ): Response {
        $models = $this->queryBus->ask(new FindUsersQuery($page, $limit));

        return $this->render('user/index.html.twig', [
            'users' => $models,
        ]);
    }

    public function new(Request $request): Response
    {
        $dto = new CreateUserDTO();
        $form = $this->createForm(UserFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->dispatch(new CreateUser($dto));

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'form' => $form,
        ]);
    }
}
