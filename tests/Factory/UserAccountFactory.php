<?php

declare(strict_types=1);

namespace Tests\Factory;

use App\Module\Auth\Domain\Enum\Role;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
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
            'id'  => Uuid::fromString(self::faker()->uuid()),
            'email' => self::faker()->unique()->safeEmail(),
            'isVerified' => true,
            'password' => '$2y$13$IsuGpPQSY/sVXtC/TzJm4.167OchtW/fepPOKPiskbjBlXxPCVpJS',
            'roles' => [Role::USER],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->instantiateWith(function (array $attributes): UserAccount {
                // Étape 1 : Créer l'objet via la méthode de fabrique statique.
                // Note : On passe le mot de passe en clair à ce stade, comme le fait le RegisterUserHandler.
                $user = UserAccount::register(
                    UserId::generate($attributes['id']),
                    new Email($attributes['email'])
                );

                // Étape 3 (optionnel) : Marquer l'utilisateur comme vérifié si demandé.
                if ($attributes['isVerified']) {
                    $user->verified();
                }

                return $user;
            });
    }
}
