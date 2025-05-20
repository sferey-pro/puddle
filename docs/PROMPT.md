"Rappelle-toi du projet 'Puddle'. C'est une application backend développée en PHP avec le framework Symfony. 
Mon objectif principal avec 'Puddle' est de **mettre en valeur mes compétences dans la mise en œuvre d'architectures logicielles modernes**, notamment le Domain-Driven Design (DDD) et l'Architecture Hexagonale (Ports & Adapters). 
Je cherche également à **utiliser et maîtriser un maximum de composants Symfony** pour approfondir ma connaissance du framework.
Le projet a vocation à être utilisé plus tard, soit pour **mes besoins personnels, soit pour servir de boilerplate** pour de futurs projets. J'ai également un dossier `docs/Challenge/` dans lequel je souhaite **compléter une série de défis techniques** pour enrichir le projet et mes apprentissages.

L'application met l'accent sur les principes du DDD, de l'Architecture Hexagonale, du CQRS et une approche orientée événements.

Les fonctionnalités principales sont actuellement centrées autour de :
1.  **Module d'Authentification (`Auth`)** : gestion des inscriptions, connexions (classique et via OAuth), sécurité des comptes.
2.  **Module de Gestion des Utilisateurs (`UserManagement`)** : gestion des profils utilisateurs, informations personnelles.
3.  **Contexte Partagé (`SharedContext`)** : éléments communs comme les Value Objects (UserId, Email).

Techniquement, le projet utilise :
* PHP 8.x
* Symfony (avec notamment Symfony Messenger pour les bus de commandes/queries/événements, et une exploration d'autres composants comme Security, Forms, Workflow, Notifier, etc.)
* Doctrine ORM pour la persistance des données d'écriture (Write Models).
* MongoDB pour la gestion des modèles de lecture (Read Models), dans le cadre de l'approche CQRS.
* Composer pour la gestion des dépendances.

L'objectif général est de construire une application modulaire, testable et évolutive, avec une séparation claire des préoccupations entre le domaine métier et l'infrastructure. La communication entre modules se fait principalement via des événements de domaine. 
Le projet explore aussi des concepts avancés comme les Sagas pour la gestion de processus distribués entre les modules (documentés dans `docs/Challenge/Saga.md`)."
