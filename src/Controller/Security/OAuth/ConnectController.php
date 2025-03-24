<?php

declare(strict_types=1);

namespace App\Controller\Security\OAuth;

use App\Config\SocialNetwork;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;

#[Route(
    '/oauth/connect/{socialNetwork}',
    requirements: [
        'socialNetwork' => new EnumRequirement(SocialNetwork::class)
    ],
    name: 'security_oauth_connect',
    methods: [
        Request::METHOD_GET
    ]
)]
final class ConnectController extends AbstractController
{
    public const SCOPES = [
        SocialNetwork::GOOGLE->value => ['email', 'profile'],
        SocialNetwork::GITHUB->value => ['user:email'],
    ];

    public function __invoke(
        SocialNetwork $socialNetwork,
        ClientRegistry $clientRegistry
    ): RedirectResponse {

        /** @var OAuth2Client $client */
        $client = $clientRegistry
            ->getClient($socialNetwork->value);

        return $client->redirect(self::SCOPES[$socialNetwork->value]);
    }
}
