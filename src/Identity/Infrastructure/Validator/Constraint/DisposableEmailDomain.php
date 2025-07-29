<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Contrainte pour rejeter les domaines email jetables.
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class DisposableEmailDomain extends Constraint
{
    public string $message = 'The email domain "{{ domain }}" is not allowed.';

    /**
     * Liste des domaines jetables connus (à enrichir).
     * En production, utiliser un service externe ou une DB.
     */
    public array $blockedDomains = [
        'mailinator.com',
        'guerrillamail.com',
        '10minutemail.com',
        'tempmail.com',
        'throwaway.email',
        'yopmail.com',
        'trashmail.com',
        'getnada.com',
        'temp-mail.org',
        'fakeinbox.com',
    ];

    public function __construct(
        ?array $options = null,
        ?string $message = null,
        ?array $blockedDomains = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->blockedDomains = $blockedDomains ?? $this->blockedDomains;
    }
}
