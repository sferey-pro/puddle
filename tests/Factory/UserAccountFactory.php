<?php

declare(strict_types=1);

namespace Tests\Factory;

use App\Module\Auth\Domain\Enum\Role;
use App\Module\Auth\Domain\UserAccount;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<UserAccount>
 */
final class UserAccountFactory extends PersistentProxyObjectFactory
{
    public const DEFAULT_PASSWORD = '1234'; // the password used to create the pre-encoded version below

    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return UserAccount::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'uuid'  => Uuid::fromString(self::faker()->uuid()),
            'isVerified' => true,
            'password' => '$2y$13$IsuGpPQSY/sVXtC/TzJm4.167OchtW/fepPOKPiskbjBlXxPCVpJS',
            'roles' => [Role::USER],
        ];
    }
}
