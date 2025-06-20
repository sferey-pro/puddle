# Couche Domaine : Le Cœur Métier

La couche Domaine est le cœur de notre application. Elle contient la logique métier, les règles et les processus qui sont propres à notre domaine d'activité. Elle est totalement agnostique de la technologie et ne doit dépendre d'aucune autre couche, ce qui en fait le composant le plus pur et le plus testable de notre système.

## Principes Fondamentaux

### Immutabilité et Accès aux Données (Règle d'Or)

Tous les objets du Domaine (Agrégats, Entités, VOs, Événements) suivent la même règle pour garantir la prévisibilité et la robustesse du code. L'immutabilité est le choix par défaut.

> Les propriétés sont déclarées en **`private readonly`**. Les données sont exposées via des **getters publics sans préfixe `get`**.

```php
final readonly class CostItemName
{
    public function __construct(private string $name) 
    {
        // La validation d'invariant a lieu ici
        Assert::notEmpty($name);
    }

    public function name(): string
    {
        return $this->name;
    }
}
```

## Blocs de Construction (Building Blocks)

Notre domaine est construit à partir des blocs de base de Domain-Driven Design.

### Value Objects (VOs)

Les Value Objects représentent des concepts du domaine définis par leurs attributs. Ils sont égaux si tous leurs attributs sont égaux.

* **Implémentation :** Ils doivent être `final readonly`.
* **Validation :** Un VO est responsable de sa propre validité. Il doit garantir sa cohérence dès sa création via des assertions dans son constructeur (`webmozart/assert`).
* **Exemple :** `Email`, `Money`, `IpAddress`, `LoginLinkDetails`.

### Entités (Entities)

Les Entités ont une identité qui perdure dans le temps et à travers les changements d'états, même si leurs attributs changent.

* **Identité :** L'identité est définie par un ID unique (généralement un VO dédié comme `UserId`, `LoginLinkId`).
* **Immutabilité :** Leurs propriétés doivent être `readonly` autant que possible. Les changements d'état ne se font que via des méthodes de comportement explicites qui protègent les invariants.
* **Exemple :** `LoginLink`, `SocialLink`.

### Agrégats (Aggregate Roots)

Un agrégat est un cluster d'entités et de VOs qui est traité comme une unité transactionnelle cohérente. La racine de l'agrégat (Aggregate Root) est le seul point d'entrée pour les modifications.

* **Responsabilité :** L'agrégat est le gardien des invariants (règles métier) pour l'ensemble des objets qu'il contient.
* **Accès :** Seuls les agrégats peuvent être chargés directement depuis un Repository. On ne charge jamais une entité interne (comme `LoginLink`) directement ; on passe toujours par son agrégat (`UserAccount`).
* **Exemple :** `UserAccount` est un agrégat qui contient des entités `LoginLink`.

### Événements de Domaine (Domain Events)

Un Événement de Domaine est un objet qui représente quelque chose de significatif qui s'est produit dans le domaine.

* **Nommage :** Toujours au passé (ex: `UserRegistered`, `OrderShipped`, `LoginLinkGenerated`).
* **Contenu :** Doit contenir des données primitives ou des IDs (Value Objects), mais jamais des entités ou des agrégats complets pour éviter de créer des couplages forts.
* **Implémentation :** Les classes d'événement sont `final readonly` et étendent notre classe `DomainEvent` de base.

### Exceptions de Domaine

Les exceptions sont la manière dont le domaine communique une violation de ses règles métier. Pour garantir la clarté et la cohésion, nous suivons un pattern précis pour leur implémentation.

> Chaque **Agrégat** ou **Entité principale** doit avoir son **propre et unique fichier d'exception**. Cette classe regroupe toutes les erreurs métier possibles pour cet objet.

* **Héritage :** Toutes nos exceptions de domaine doivent hériter de la classe de base `\DomainException`.
* **Implémentation :**
    * La classe d'exception doit être `final`.
    * Le constructeur doit être `private` pour forcer l'utilisation de constructeurs statiques nommés.
    * Chaque type d'erreur métier est représenté par une **méthode statique nommée** (ex: `notFoundWithId()`, `expired()`). C'est cette méthode qui est responsable de construire l'exception avec un message clair et un code d'erreur unique.
    * Chaque erreur est associée à un **code d'erreur unique** et stable, défini comme une constante privée, qui peut être exploité par les couches supérieures (ex: pour des traductions ou des réponses API spécifiques).

**Exemple :** Fichier d'exception pour l'agrégat `UserAccount`.

```php
<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Exception;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Exception métier unique pour l'agrégat UserAccount.
 */
final class UserException extends \DomainException
{
    private const NOT_FOUND = 'U-001';
    private const EMAIL_ALREADY_EXISTS = 'U-002';

    private function __construct(string $message, private readonly string $errorCode)
    {
        parent::__construct($message);
    }

    public static function notFoundWithId(UserId $id): self
    {
        return new self(\sprintf('User with ID "%s" not found.', $id), self::NOT_FOUND);
    }

    public static function emailAlreadyExists(Email $email): self
    {
        return new self(
            \sprintf('A user with the email "%s" already exists.', $email),
            self::EMAIL_ALREADY_EXISTS
        );
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
```

### Services de Domaine (Domain Services)

Un Service de Domaine encapsule une logique métier qui ne trouve sa place naturelle dans aucune entité, notamment lorsqu'un processus complexe requiert une coordination.

> Il agit comme un **chef d'orchestre** : il prend une requête applicative (via un Command Handler), dialogue avec d'autres services ou des ports du domaine (ex: un générateur externe) pour préparer une action, puis appelle une méthode sur l'**agrégat** concerné pour que celui-ci applique la modification. Cette approche préserve la pureté de l'agrégat, qui reste maître de ses invariants, et garantit que chaque objet conserve une responsabilité unique et claire.

* **Implémentation :** Les services de domaine sont **stateless**, `final readonly`, et leurs dépendances (toujours des interfaces/ports) sont injectées via le constructeur.
* **Exemple :** `LoginLinkManager`.

### Ports (Interfaces de Domaine)

Les ports sont des interfaces définies dans le domaine qui décrivent un contrat pour une fonctionnalité dont l'implémentation est externe. C'est le cœur de l'Architecture Hexagonale.

* **Objectif :** Permettre au domaine de rester indépendant des détails techniques. Le domaine définit un besoin (`UserRepositoryInterface`), et l'infrastructure fournit une ou plusieurs implémentations (`DoctrineUserRepository`).
* **Exemples :** `UserRepositoryInterface`, `LoginLinkGeneratorInterface`, `ClockInterface`.

### Spécifications (Specification Pattern)

Le pattern Spécification est utilisé pour encapsuler des règles métier complexes et réutilisables.

* **Objectif :** Transformer une règle métier en un objet à part entière. Cela permet de la nommer, de la tester isolément et de la combiner avec d'autres règles (via `AndSpecification`, `OrSpecification`, `NotSpecification`).
* **Exemple :** `UserCanBeAnonymizedSpecification` encapsule toutes les conditions nécessaires pour qu'un utilisateur puisse être anonymisé.
