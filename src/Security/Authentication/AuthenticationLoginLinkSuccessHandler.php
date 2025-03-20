<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use App\Entity\UserLogin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationLoginLinkSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    protected array $defaultOptions = [
        'default_target_path' => '/',
    ];

    public function __construct(
        private HttpUtils $httpUtils,
        private EntityManagerInterface $em,
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        $user = $token->getUser();
        $date = new \DateTimeImmutable();

        /** @var UserLogin $userLogin */
        $userLogin = $this->em->getRepository(UserLogin::class)->findOneBy([
            'user' => $user,
            'expiresAt' => $date->setTimestamp((int) $request->get('expires')),
            'hash' => $request->get('hash'),
        ]);

        $userLogin->verified();

        $this->em->persist($userLogin);
        $this->em->flush();

        return $this->httpUtils->createRedirectResponse($request, $this->defaultOptions['default_target_path']);
    }
}
