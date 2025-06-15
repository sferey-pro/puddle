<?php

declare(strict_types=1);

namespace App\Module\UserManagement\UI\Controller;

use App\Module\UserManagement\Application\Command\CreateUser;
use App\Module\UserManagement\Application\DTO\CreateUserDTO;
use App\Module\UserManagement\Application\Query\ListUsersQuery;
use App\Module\UserManagement\UI\Form\UserFormType;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\Doctrine\Paginator;
use Symfony\Bridge\Twig\Attribute\Template;
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

    #[Template('@UserManagement/user/index.html.twig')]
    public function index(
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $page = 1,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $limit = Paginator::PAGE_SIZE,
    ): array {
        $models = $this->queryBus->ask(new ListUsersQuery($page, $limit));

        return [
            'users' => $models,
        ];
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

        return $this->render('@UserManagement/user/new.html.twig', [
            'form' => $form,
        ]);
    }
}
