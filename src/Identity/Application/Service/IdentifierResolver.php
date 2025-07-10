<?php

declare(strict_types=1);

namespace Identity\Application\Service;

use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Domain\Result;

final class IdentifierResolver
{
    /**
     * @return Result<Identifier>
     */
    public static function resolve(string $identifier): Result
    {
        $result = EmailIdentity::create($identifier);
        if ($result->isSuccess()) {
            return $result;
        }

        $result = PhoneIdentity::create($identifier);
        if ($result->isSuccess()) {
            return $result;
        }

        // ... ajouter d'autres résolveurs ici

        return Result::failure(new \DomainException("L'identifiant fourni ne correspond ni à un format d'email, ni à un format de numéro de téléphone valide."));
    }
}
