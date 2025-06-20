# 1. Principes d'Architecture Généraux

Notre architecture est une combinaison de **Domain-Driven Design (DDD)**, **Architecture Hexagonale (Ports & Adapters)** et **CQRS**.

* **Write Model (Écriture) :** Centré sur le Domaine, la logique métier et les invariants. Il utilise PostgreSQL avec Doctrine ORM.
* **Read Model (Lecture) :** Optimisé pour l'affichage. Les données sont "plates" et stockées dans MongoDB.
* **Communication :** Les **Événements de Domaine** (Domain Events) sont publiés sur un **Event Bus** (Symfony Messenger) pour assurer la communication asynchrone entre le Write et le Read Model.
