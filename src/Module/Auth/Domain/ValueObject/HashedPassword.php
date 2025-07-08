<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Module\Auth\Domain\Service\PasswordHasherInterface;
use Webmozart\Assert\Assert;

/**
 * Représente un mot de passe qui a été haché de manière sécurisée.
 *
 * Cet objet garantit qu'on ne manipule jamais le hash directement et que les opérations
 * de création et de vérification sont toujours faites via le service de hachage adéquat.
 */
final readonly class HashedPassword implements \Stringable
{
    public string $value;

    /**
     * Le constructeur est privé pour forcer l'utilisation des factories nommées,
     * ce qui garantit que l'objet est toujours dans un état valide.
     */
    private function __construct(string $value)
    {
        Assert::notEmpty($value, 'Hashed password cannot be empty.');
    }

    /**
     * Factory principale : crée un HashedPassword à partir d'un mot de passe en clair.
     * C'est la méthode à utiliser dans les Command Handlers (ex: ChangePasswordHandler).
     */
    public static function createFromPlainText(string $plainPassword, PasswordHasherInterface $hasher): self
    {
        $hash = $hasher->hash($plainPassword);
        return new self($hash);
    }
    /**
     * Factory secondaire : crée un HashedPassword à partir d'un hash existant.
     * Utile pour les cas où le hash est déjà stocké (ex: lors de la récupération d'un utilisateur).
     */
    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    /**
     * Vérifie si un mot de passe en clair correspond au hash stocké.
     * C'est la seule façon de comparer les mots de passe.
     */
    public function matches(string $plainPassword, PasswordHasherInterface $hasher): bool
    {
        return $hasher->verify($this->value, $plainPassword);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->value;
    }
}
