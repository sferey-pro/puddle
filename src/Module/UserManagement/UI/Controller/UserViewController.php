<?php

declare(strict_types=1);

namespace App\Module\UserManagement\UI\Controller;

use App\Module\UserManagement\Application\Query\ListUsersQuery;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\Doctrine\Paginator;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

final class UserViewController extends AbstractController
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    #[Template('user/index.html.twig')]
    public function index(
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $page = 1,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $limit = Paginator::PAGE_SIZE,
    ): array {
        $models = $this->queryBus->ask(new ListUsersQuery($page, $limit));
        dd(\count($models));

        return [
            'users' => $models,
        ];
    }
}
