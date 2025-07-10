<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Service;

use App\Core\Domain\Exception\DomainException;
use App\Core\Domain\Result;
use App\Module\Auth\Domain\ValueObject\EmailIdentity;
use App\Module\Auth\Domain\ValueObject\PhoneIdentity;

final class IdentifierResolver
{
    /**
     * @return Result<UserIdentity>
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
