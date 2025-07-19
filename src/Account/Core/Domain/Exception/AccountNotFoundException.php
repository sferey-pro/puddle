<?php

declare(strict_types=1);

namespace Account\Core\Domain\Exception;

final class AccountNotFoundException extends \DomainException
{
    public function __construct($id = null, array $criteria = [])
    {
        $message = 'Account not found.';

        if ($id !== null) {
            $message .= " ID: {$id}";
        } elseif (!empty($criteria)) {
            $message .= " Criteria: " . json_encode($criteria);
        }
        
        parent::__construct($message);
    }
    
}
