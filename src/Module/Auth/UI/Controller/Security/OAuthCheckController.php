<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class OAuthCheckController extends AbstractController
{
    public function __invoke(): Response
    {
        return new Response(status: 200);
    }
}
