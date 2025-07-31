<?php

declare(strict_types=1);

namespace Authentication\Application\Service;

use Authentication\Domain\Model\LoginRequest;
use Authentication\Domain\Specification\AccountCanLoginSpecification;
use Authentication\Domain\Specification\LoginAttemptRateLimitSpecification;
use Identity\Domain\Specification\IdentifierFormatIsValidSpecification;

/**
 * Service qui combine toutes les validations de connexion.
 */
final readonly class LoginValidationService
{
    public function __construct(
        private IdentifierFormatIsValidSpecification $identifierFormat,
        private AccountCanLoginSpecification $accountCanLogin,
        private LoginAttemptRateLimitSpecification $rateLimitCheck,
    ) {}

    public function validateLoginRequest(LoginRequest $request): LoginValidationResult
    {
        // 1. Format de l'identifiant
        if (!$this->identifierFormat->isSatisfiedBy($request->identifier)) {
            return LoginValidationResult::failed(
                'invalid_identifier_format',
                $this->identifierFormat->failureReason()
            );
        }

        // 2. Rate limiting (vérifier AVANT de chercher le compte)
        if (!$this->rateLimitCheck->isSatisfiedBy($request)) {
            return LoginValidationResult::failed(
                'rate_limit_exceeded',
                $this->rateLimitCheck->failureReason()
            );
        }

        // 3. État du compte (après avoir vérifié que l'identifiant est valide)
        if (!$this->accountCanLogin->isSatisfiedBy($request)) {
            return LoginValidationResult::failed(
                'account_cannot_login',
                $this->accountCanLogin->failureReason()
            );
        }

        return LoginValidationResult::success();
    }
}
