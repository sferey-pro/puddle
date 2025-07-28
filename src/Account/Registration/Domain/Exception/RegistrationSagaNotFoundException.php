<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Exception;

final class RegistrationSagaNotFoundException extends \DomainException
{
    public function __construct($id = null, array $criteria = [])
    {
        $message = 'Registration Saga not found.';

        if ($id !== null) {
            $message .= " ID: {$id}";
        } elseif (!empty($criteria)) {
            $message .= " Criteria: " . json_encode($criteria);
        }

        parent::__construct($message);
    }
}
