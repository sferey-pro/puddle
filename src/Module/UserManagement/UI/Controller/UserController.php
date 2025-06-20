<?php

declare(strict_types=1);

namespace App\Module\UserManagement\UI\Controller;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\Query\FindUserQuery;
use App\Module\UserManagement\Application\Query\ListUsersQuery;
use App\Module\UserManagement\Application\ReadModel\UserView;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\Doctrine\Paginator;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Template('@UserManagement/user/new.html.twig')]
    public function new(): void
    {
    }

    #[Template('@UserManagement/user/edit.html.twig')]
    public function edit(Request $request): array
    {
        $id = UserId::fromString($request->get('id'));
        $instanceView = $this->findUserView($id);

        return [
            'user' => $instanceView,
        ];
    }

    private function findUserView(UserId $userId): UserView
    {
        return $this->queryBus->ask(new FindUserQuery($userId));
    }
}
