<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/login/check', name: 'app_login_check')]
final class LoginCheckController extends AbstractController
{
    #[Template('security/process_login_link.html.twig')]
    public function __invoke(
        Request $request,
    ): array {
        // get the login link query parameters
        $expires = $request->query->get('expires');
        $username = $request->query->get('user');
        $hash = $request->query->get('hash');

        // and render a template with the button
        return [
            'expires' => $expires,
            'user' => $username,
            'hash' => $hash,
        ];
    }
}
