<?php

declare(strict_types=1);

namespace Identity\Domain\Exception;

final class InvalidIdentifierException extends \DomainException
{
    public static function fromResolutionError(string $rawIdentifier, string $reason): self
    {
        return new self("Identifiant invalide '{$rawIdentifier}' : {$reason}");
    }

    public static function unsupportedType(string $type): self
    {
        return new self("Type d'identifiant non supporté : {$type}");
    }

    public static function malformedEmail(string $email): self
    {
        return new self("Format d'email invalide : {$email}");
    }

    public static function malformedPhone(string $phone): self
    {
        return new self("Format de téléphone invalide : {$phone}");
    }
}
