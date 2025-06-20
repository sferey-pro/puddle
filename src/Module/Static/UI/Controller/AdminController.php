<?php

declare(strict_types=1);

namespace App\Module\Static\UI\Controller;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    public function __construct(
        private readonly UserViewRepositoryInterface $viewRepository,
    ) {
    }

    #[Template('admin/index.html.twig')]
    public function __invoke(): void
    {
        $userView = $this->viewRepository->findById(UserId::fromString('01978e9d-a933-7957-b9c5-8c751c0a125f'));

        $userView->setIsVerified(false);

        $this->viewRepository->save($userView, true);

        $users = $this->viewRepository->findAll();

        dd($userView, $users);
    }
}
