# Puddle

Bienvenue sur le projet Puddle ! Ce projet est une application backend développée en PHP, servant de terrain d'exploration et d'implémentation de concepts architecturaux modernes tels que le Domain-Driven Design (DDD) et l'Architecture Hexagonale.

# Important 

Le développement suit une approche itérative, où la priorité est la progression continue sur la fonctionnalité. L'historique des commits reflète ce flux de travail exploratoire plutôt qu'une sémantique stricte.

## Objectif du Projet

L'objectif principal est de construire une application robuste, maintenable et évolutive en appliquant des bonnes pratiques de conception logicielle. Il sert également de base pour expérimenter divers patterns et outils (voir la section [Challenges et Explorations](#challenges-et-explorations)).

Actuellement, le projet se concentre sur les fonctionnalités de gestion des utilisateurs et d'authentification.

## Concepts Architecturaux Clés

Ce projet s'efforce de mettre en œuvre les principes suivants :

*   **Domain-Driven Design (DDD) :** Le modèle métier est au cœur de l'application. Nous utilisons des concepts DDD tels que les Entités, les Value Objects, les Agrégats, les Services de Domaine, les Événements de Domaine et les Repositories.
*   **Architecture Hexagonale (Ports & Adapters) :** L'application (le cœur métier) est isolée des préoccupations d'infrastructure (bases de données, frameworks web, etc.). Les interactions se font via des ports (interfaces définies par l'application) et des adaptateurs (implémentations concrètes de ces ports).
*   **Command Query Responsibility Segregation (CQRS) :** Les opérations de modification de l'état (Commandes) sont séparées des opérations de lecture de l'état (Queries). Cela permet d'optimiser chaque côté indépendamment.
*   **Event-Driven Architecture :** Les changements d'état significatifs dans le domaine sont capturés et diffusés sous forme d'Événements de Domaine. D'autres parties du système peuvent réagir à ces événements de manière découplée.

## Structure des Modules

L'application est organisée en modules, chacun représentant un Contexte Délimité (Bounded Context) du domaine :

*   **`src/Module/UserManagement` :** Gère les informations relatives au profil des utilisateurs (création, lecture, mise à jour des données utilisateur).
*   **`src/Module/Auth` :** Responsable de l'enregistrement, de l'authentification et des aspects liés à la sécurité des comptes utilisateurs.
*   **`src/Module/Shared` :** Contient les éléments communs utilisés par plusieurs modules (Value Objects partagés, interfaces de bus, etc.).
*   **Et d'autres**

## Technologies Principales (non exhaustif)

*   **Langage :** PHP 8.x
*   **Framework :** Symfony (utilisation de composants tels que Messenger pour les bus de Commandes/Queries/Événements, PasswordHasher, etc.)
*   **Persistance :** Doctrine ORM (pour l'interaction avec la base de données)
*   **ReadModel :** Doctrine ODM (pour l'affichage des données)
*   **Gestion des dépendances :** Composer


## Challenges et Explorations

Ce projet sert également de bac à sable pour explorer et mettre en pratique des concepts et outils plus avancés. Vous trouverez une documentation dédiée à ces explorations dans le dossier `/home/sferey/puddle/docs/Challenge/` :

*   `Saga.md` : Réflexion sur l'implémentation du pattern Saga pour gérer les transactions distribuées.
*   `EventSourcing.md` : Exploration de l'Event Sourcing comme alternative au stockage d'état traditionnel.
*   `Tools.md` : Liste des outils (Elasticsearch, Stripe, etc.) envisagés pour enrichir le projet.
*   *(D'autres challenges à venir...)*

---

*Ce README est un document vivant et sera mis à jour au fur et à mesure de l'évolution du projet.*
