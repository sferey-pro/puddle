<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller\Security\OAuth;

use App\Module\Auth\Domain\Enum\SocialNetwork;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ConnectController extends AbstractController
{
    public const SCOPES = [
        SocialNetwork::GOOGLE->value => ['email', 'profile'],
        SocialNetwork::GITHUB->value => ['user:email'],
    ];

    public function __invoke(
        SocialNetwork $socialNetwork,
        ClientRegistry $clientRegistry,
    ): RedirectResponse {
        /** @var OAuth2Client $client */
        $client = $clientRegistry
            ->getClient($socialNetwork->value);

        return $client->redirect(self::SCOPES[$socialNetwork->value]);
    }
}
