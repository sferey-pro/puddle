# Le Répertoire `Core`

## Rôle et Philosophie

Le répertoire `Core` est le cœur technique et architectural de l'application. Il contient les briques fondamentales et les implémentations de patterns qui soutiennent l'ensemble de nos modules.

**Sa règle d'or est simple : il ne contient AUCUNE logique métier ou de domaine.**

Ce répertoire définit **comment** l'application fonctionne (les bus, les patterns, les contrats), mais jamais **ce qu'elle fait** (la gestion d'un utilisateur, une commande, etc.). Il est le garant de la cohérence et de la robustesse de notre architecture.

---

## Contenu Autorisé

### 1. Couche `Application` (`Core/Application`)

Cette couche définit les **contrats (interfaces)** de nos patterns architecturaux. Elle est la porte d'entrée technique pour nos modules.

* **Interfaces des Bus CQRS** :
    * `CommandBusInterface.php`
    * `QueryBusInterface.php`
    * `EventBusInterface.php`
* **Interfaces des Messages** :
    * `CommandInterface.php`
    * `QueryInterface.php`

### 2. Couche `Domain` (`Core/Domain`)

Cette couche héberge les **abstractions de base** pour nos objets de domaine, conformément au DDD.

* **Abstractions d'Agrégats** :
    * `Aggregate/AggregateRoot.php`
* **Abstractions d'Événements** :
    * `Event/DomainEvent.php`
    * `Event/DomainEventInterface.php`

### 3. Couche `Infrastructure` (`Core/Infrastructure`)

Cette couche contient les **implémentations concrètes** et la plomberie technique, souvent liées à des bibliothèques externes comme Symfony ou Doctrine.

* **Implémentations des Bus** :
    * `Bus/Messenger/MessengerCommandBus.php`
    * `Bus/Messenger/MessengerQueryBus.php`
* **Subscribers Techniques** :
    * `Symfony/EventSubscriber/SystemTimeInitializerSubscriber.php`
* **Classes de Base pour les Patterns** :
    * `Saga/Process/AbstractSagaProcess.php`
    * `Specification/SpecificationInterface.php`

---

## À ne JAMAIS mettre ici :

* Des `ValueObjects` métier (ex: `UserId`, `OrderId`). Leur place est dans le `SharedContext` ou un module spécifique.
* Des entités de domaine (ex: `User`, `Product`).
* Des Handlers ou des Services contenant des règles métier.
