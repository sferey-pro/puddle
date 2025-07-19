<?php
declare(strict_types=1);

namespace Authentication\Domain;

use Account\Core\Domain\Model\Account;

class User
{
    public $id;
    public $identifier;
    public $password;

    public static function fromAccount(Account $account): self
    {
        $user = new User();
        $user->id = $account->id;
        $user->identifier = $account->identifier;

        return $user;
    }
}
