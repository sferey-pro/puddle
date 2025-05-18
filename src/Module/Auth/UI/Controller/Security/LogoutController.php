<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class LogoutController extends AbstractController
{
    public function __invoke(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
