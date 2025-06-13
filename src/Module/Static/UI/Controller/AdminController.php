<?php

declare(strict_types=1);

namespace App\Module\Static\UI\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Template('admin/index.html.twig')]
    public function __invoke(): void
    {
    }
}
