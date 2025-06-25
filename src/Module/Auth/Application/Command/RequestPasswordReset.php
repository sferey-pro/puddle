<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandInterface;

final readonly class RequestPasswordReset implements CommandInterface
{
    public const EXPIRES_AT_TIME = '+1 hour';

    /**
     * @param string $email     L'adresse e-mail fournie par l'utilisateur pour laquelle la réinitialisation est demandée
     * @param string $ipAddress L'adresse IP de l'utilisateur effectuant la demande, à des fins d'audit et de sécurité
     */
    public function __construct(
        public string $email,
        public string $ipAddress,
    ) {
    }
}
