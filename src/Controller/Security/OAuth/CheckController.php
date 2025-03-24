<?php

declare(strict_types=1);

namespace App\Controller\Security\OAuth;

use App\Config\SocialNetwork;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;

#[Route(
    '/oauth/check/{socialNetwork}',
    requirements: [
        'socialNetwork' => new EnumRequirement(SocialNetwork::class)
    ],
    name: 'security_oauth_check',
    methods: [
        Request::METHOD_GET,
        Request::METHOD_POST
    ]
)]
final class CheckController extends AbstractController
{
    public function __invoke(): Response {
        return new Response(status: 200);
    }
}
