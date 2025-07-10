<?php

declare(strict_types=1);

namespace Account\Registration\Presentation\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class RegistrationController extends AbstractController
{
    #[Template('@Account/registration/register.html.twig')]
    public function register(): void
    {
    }
}
