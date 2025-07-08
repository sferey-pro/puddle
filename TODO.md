## Domain Event

Ajouter : 
    public static function eventName(): string
    {
        return 'sales.order.created';
    }


## Amélioration Context IA

AggregateRootId sont écrit comme ceci : 

final class UserId extends AggregateRootId
{
    
}

Transformation de ValueObject en string comme ceci : 
 (string) $userId

Les Enums contiennent tous une méthode getLabel pour afficher.

DomainEvent comme ceci : 

public function __construct(
        private readonly CostItemId $costItemId,
        public readonly ?ProductId $newSourceProductId = null,
) {
    parent::__construct();
}


### 

# Demande d'Architecture : Système d'Autorisation par Permissions (Claims-Based)

## Contexte Général

Je construis une application basée sur un **Monolithe Modulaire** avec **Symfony 7+** et **PHP 8.3+**. L'architecture suit les principes du **Domain-Driven Design (DDD)** et de **CQRS**.

L'application possède plusieurs Bounded Contexts, dont deux sont critiques pour cette demande :
1.  **`Auth`** : Responsable de l'**authentification** (prouver l'identité).
2.  **`UserManagement`** : Responsable de la **gestion métier** des utilisateurs (profil, statut, rôles métier).

Le défi est de mettre en place un système d'autorisation où les décisions d'attribution de droits sont prises dans `UserManagement`, mais où le contrôle d'accès est appliqué par `Auth` et le composant de sécurité de Symfony, tout en maintenant un découplage maximal entre les deux contextes.

## Objectifs Architecturaux

L'objectif est de remplacer un système de rôles traditionnel (`ROLE_ADMIN`) par une architecture de permissions (ou "claims") plus fine et plus flexible.

1.  **Découplage Maximum** : Le contexte `Auth` doit être **totalement ignorant** de la notion de `BusinessRole` qui n'existe que dans `UserManagement`. Le seul contrat partagé sera une liste de permissions.
2.  **Source de Vérité Unique** : Le contexte `UserManagement` est la **seule source de vérité** pour la définition des rôles métier (`BusinessRole`) et pour la **traduction** de ces rôles en une liste de permissions granulaires.
3.  **Granularité** : Les contrôles de sécurité dans l'application (`is_granted`) doivent se baser sur des **permissions spécifiques** (ex: `user:suspend`) et non sur des rôles génériques.
4.  **Flexibilité** : L'ajout ou la modification d'un `BusinessRole` et des permissions qu'il octroie ne doit nécessiter des changements **que dans le contexte `UserManagement`**, sans aucun impact sur le contexte `Auth`.

## Composants à Mettre en Place

Je demande une spécification technique et le code complet pour les fichiers suivants, en respectant les principes `final`, `readonly` et `declare(strict_types=1);`.

### 1. Shared Context (Le Contrat)

-   **`Permission.php` (Enum)** : Créer un `Enum` qui définit toutes les permissions granulaires de l'application (ex: `USER_SUSPEND = 'user:suspend'`). Ce sera le langage commun partagé.

### 2. Contexte `UserManagement` (Le Décideur)

-   **`BusinessRole.php` (Enum)** : Créer un `Enum` pour les rôles métier (ex: `CONTRIBUTOR`, `MODERATOR`).
-   **`RolePermissionMapper.php` (Service de Domaine)** : Créer une classe responsable de la traduction d'un `BusinessRole` en une liste de `Permission[]`. C'est ici que la logique de mapping doit être encapsulée.
-   **`User.php` (Agrégat)** : Mettre à jour l'agrégat `User` pour qu'il utilise `BusinessRole`. Sa méthode `grantBusinessRole` doit utiliser le `RolePermissionMapper` pour obtenir les permissions associées.
-   **`BusinessRoleGranted.php` (Événement de Domaine)** : Mettre à jour cet événement pour qu'il transporte la liste des `Permission[]` accordées, en plus du nom du rôle.

### 3. Contexte `Auth` (Le Gardien Technique)

-   **`UserAccount.php` (Agrégat)** : Simplifier cet agrégat. Il ne doit plus contenir de notion de `Role` ou `Roles`. Il doit posséder une unique propriété `private array $permissions = [];` (une liste de `string`). Il doit exposer une méthode `syncPermissions(array $permissions)`.
-   **ACL (EventHandler)** : Créer le handler `WhenBusinessRoleGrantedThenSyncPermissions` qui écoute l'événement de `UserManagement`. Ce handler doit être très simple : il extrait la liste des permissions de l'événement et appelle `syncPermissions` sur l'agrégat `UserAccount` correspondant. **Il ne doit contenir aucune logique de mapping.**
-   **`SecurityUser.php` (Adapter Infrastructure)** : Adapter cette classe pour qu'elle accepte une liste de permissions. Sa méthode `getRoles()` doit retourner cette liste de permissions (éventuellement fusionnée avec un `ROLE_USER` de base).
-   **`UserProvider.php` (Infrastructure)** : Adapter ce service pour qu'il charge le `UserAccount` et peuple le `SecurityUser` avec la liste de ses permissions.

### 4. Sécurité Symfony

-   Montrer par un exemple comment utiliser ces nouvelles permissions dans un contrôleur avec l'attribut `#[IsGranted('user:suspend')]`.
