<?php

namespace App\Twig\Extension;

use App\Config\SocialNetwork;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OauthUrlExtension extends AbstractExtension
{
    public function __construct(
        private UrlGeneratorInterface $generator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('oauthUrl', [$this, 'generateOauthUrl']),
        ];
    }

    public function generateOauthUrl(SocialNetwork $socialNetwork): string
    {
        return $this->generator->generate('security_oauth_connect', ['socialNetwork' => $socialNetwork->value]);
    }
}
