<?php

namespace Identity\Infrastructure\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class DisposableEmailDomainValidator extends ConstraintValidator
{
    /**
     * Service optionnel pour vérifier les domaines jetables.
     * Peut être remplacé par une API externe en production.
     */
    public function __construct() {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof DisposableEmailDomain) {
            throw new UnexpectedTypeException($constraint, DisposableEmailDomain::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        // Extraire le domaine
        $parts = explode('@', $value);
        if (count($parts) !== 2) {
            return; // Laissons la contrainte Email gérer ce cas
        }

        $domain = strtolower($parts[1]);

        // Vérifier d'abord la liste locale
        if (in_array($domain, $constraint->blockedDomains, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ domain }}', $domain)
                ->addViolation();
            return;
        }
    }
}
