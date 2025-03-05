<?php

declare(strict_types=1);

namespace App\Messenger\Command\User;

use App\Common\Command\CommandInterface;

final class RegisterUser implements CommandInterface
{
    private string $email;

    private string $plainPassword;

    public function __construct(string $email, string $plainPassword)
    {
        $this->email = $email;
        $this->plainPassword = $plainPassword;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }
}
