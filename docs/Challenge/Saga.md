# Le Pattern Saga

## Qu'est-ce qu'une Saga ?

Une Saga est un **mécanisme de gestion de transactions distribuées**. Dans une architecture microservices ou modulaire (comme une architecture hexagonale avec des modules distincts), une seule "action métier" peut nécessiter des modifications de données dans plusieurs services ou modules différents. Chacun de ces services/modules gère généralement sa propre base de données ou son propre état.

Le problème est : comment s'assurer que toutes ces modifications sont appliquées avec succès (commit) ou qu'aucune d'entre elles n'est appliquée si l'une échoue (rollback), sans utiliser de transactions distribuées traditionnelles (type 2PC - Two-Phase Commit) qui sont souvent complexes, peu performantes et introduisent un fort couplage ?

La Saga résout ce problème en séquençant une série de **transactions locales**. Chaque transaction locale met à jour les données dans un seul service/module et publie un événement (ou envoie une commande) pour déclencher la transaction locale suivante dans la Saga.

**Caractéristiques clés :**

*   **Transactions Locales :** Chaque étape de la Saga est une transaction atomique au sein d'un seul service/module.
*   **Compensation :** Si une transaction locale échoue, la Saga doit exécuter des **transactions de compensation** pour annuler les modifications effectuées par les transactions locales précédentes qui ont réussi. Une transaction de compensation est l'inverse sémantique d'une transaction locale (par exemple, si une transaction a créé une ressource, sa compensation la supprimera).
*   **Atomicité (éventuelle) :** La Saga garantit que soit toutes les transactions locales de la séquence sont complétées avec succès, soit un sous-ensemble de transactions de compensation est exécuté pour ramener le système à un état cohérent (annulant les opérations précédentes). L'atomicité n'est pas aussi stricte qu'une transaction ACID classique sur une seule base de données, mais elle vise une cohérence métier.
*   **Durabilité :** L'état de la Saga elle-même doit souvent être persistant pour pouvoir reprendre en cas de crash.

## Types de Sagas

Il existe principalement deux manières de coordonner les Sagas :

1.  **Chorégraphie (Choreography) :**
    *   Il n'y a pas de coordinateur central.
    *   Chaque service/module participant à la Saga publie des événements lorsqu'il termine sa transaction locale.
    *   Les autres services/modules écoutent ces événements et savent s'ils doivent agir.
    *   **Avantages :** Simple à implémenter pour des Sagas courtes, couplage lâche.
    *   **Inconvénients :** Peut devenir complexe à suivre et à déboguer si la Saga implique de nombreux participants (qui écoute quoi ? quel est le flux global ?). La gestion des compensations peut être plus difficile à orchestrer.

2.  **Orchestration :**
    *   Un **orchestrateur de Saga** (un nouveau service/composant) est responsable de dire aux participants quelles transactions locales exécuter.
    *   L'orchestrateur envoie des commandes aux participants et attend leurs réponses (souvent via des événements).
    *   Si une étape échoue, l'orchestrateur est responsable d'envoyer les commandes de compensation aux participants appropriés dans l'ordre inverse.
    *   **Avantages :** Logique de la Saga centralisée, plus facile à comprendre, à gérer et à déboguer. La gestion des compensations est plus explicite.
    *   **Inconvénients :** Peut introduire un point de défaillance unique (l'orchestrateur) et un couplage plus fort avec l'orchestrateur.

## Quand utiliser une Saga ?

*   Lorsque vous avez besoin de maintenir la cohérence des données à travers plusieurs services/modules.
*   Lorsque les transactions distribuées traditionnelles (2PC) ne sont pas une option viable (complexité, performance, couplage).
*   Pour des processus métier longs où verrouiller des ressources pendant toute la durée n'est pas acceptable.

## Exemple simple : Inscription d'un utilisateur

Imaginons un processus d'inscription qui implique :
1.  Créer un profil utilisateur (Module Utilisateur).
2.  Créer les informations d'authentification (Module Authentification).
3.  Envoyer un email de bienvenue (Module Notification).

Si la création des informations d'authentification échoue après la création du profil, une Saga orchestrée pourrait :
*   Demander au Module Utilisateur de supprimer le profil créé (transaction de compensation).

---


Source :

 - [Symfony’s Workflow Component and Saga Pattern](https://kisztof.medium.com/symfonys-workflow-component-and-saga-pattern-a-comprehensive-guide-to-managing-complex-business-599a9c713b1c)
