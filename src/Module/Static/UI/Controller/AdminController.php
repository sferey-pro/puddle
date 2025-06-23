<?php

declare(strict_types=1);

namespace App\Module\Static\UI\Controller;

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
    }
}
