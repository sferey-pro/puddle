# Outils pour l'Expérience de Développement et la Qualité du Code

Ce document liste les outils et pratiques que nous envisageons d'intégrer pour améliorer l'expérience de développement, la qualité du code et la maintenabilité du projet.

## 1. Analyse Statique et Qualité du Code

### PHPStan
*   **Description :** Outil d'analyse statique pour PHP qui trouve les erreurs dans le code sans l'exécuter. Il permet de définir des niveaux de rigueur pour une adoption progressive.
*   **Cas d'usage :**
    *   Détection précoce des bugs (erreurs de type, appels de méthodes inexistantes, etc.).
    *   Amélioration de la robustesse et de la fiabilité du code.
*   **Statut :** Fortement recommandé / À mettre en place.

### Deptrac
*   **Description :** Outil d'analyse statique qui permet de définir et de vérifier les dépendances entre les couches et les modules de votre application.
*   **Cas d'usage :**
    *   S'assurer que les règles de l'Architecture Hexagonale (ou d'autres architectures en couches) sont respectées (par exemple, le domaine ne doit pas dépendre de l'infrastructure).
    *   Visualiser et contrôler les dépendances entre les modules.
*   **Statut :** Recommandé / À explorer (particulièrement pertinent pour ton architecture).

## 2. Environnements de Développement

### Dev Containers (Microsoft)
*   **Description :** Spécification et ensemble d'outils pour définir des environnements de développement reproductibles et configurés à l'aide de conteneurs Docker. S'intègre bien avec VS Code (Remote - Containers).
*   **Cas d'usage :**
    *   Simplifier l'onboarding des nouveaux développeurs.
    *   Assurer que tous les développeurs travaillent avec le même environnement et les mêmes outils.
    *   Isoler les dépendances du projet de la machine hôte.
*   **Statut :** Recommandé / À mettre en place.

### Castor (JoliCode)
*   **Description :** Un lanceur de tâches (task runner) moderne écrit en PHP, inspiré par Make, PyInvoke ou Rake. Permet de définir des tâches courantes (lancer des tests, nettoyer le cache, etc.) en PHP.
*   **Cas d'usage :**
    *   Simplifier et standardiser l'exécution des commandes de développement répétitives.
    *   Créer un "point d'entrée" unique pour les opérations de build, test, lint, etc.
*   **Statut :** Recommandé / À explorer.

## 3. Tests et Fixtures

### Infection (Mutation Testing)
*   **Description :** Framework de test par mutation pour PHP. Il modifie (mute) de petites parties de votre code source et vérifie si vos tests existants détectent ces changements (tuent les mutants). Si un mutant survit, cela indique une faiblesse potentielle dans vos tests.
*   **Cas d'usage :**
    *   Évaluer la qualité et l'efficacité de votre suite de tests unitaires.
    *   Identifier les parties du code qui ne sont pas suffisamment testées ou dont les tests sont trop indulgents.
    *   Améliorer la confiance dans la non-régression.
*   **Statut :** Recommandé / À explorer (pour améliorer la robustesse des tests).

## 4. Documentation

### Docusaurus / MkDocs
*   **Description :** Générateurs de sites de documentation statiques. Docusaurus est basé sur React, MkDocs sur Python avec des thèmes configurables.
*   **Cas d'usage :**
    *   Créer un site de documentation plus structuré et navigable pour le projet (au-delà des fichiers Markdown bruts).
    *   Intégrer la documentation technique, les guides d'architecture, etc.
*   **Statut :** À explorer (si le volume de documentation le justifie).

---
## 5. Développement et Test d'API

### Bruno
*   **Description :** Un client API open-source moderne (similaire à Postman/Insomnia) qui stocke les collections directement dans le système de fichiers en utilisant un langage de balisage simple, Bru.
*   **Cas d'usage :**
    *   Concevoir, tester et documenter des API.
    *   Faciliter la collaboration sur les collections d'API grâce au versionnement Git (puisque les collections sont des fichiers).
    *   Intégrer les tests d'API dans les pipelines CI/CD.
*   **Statut :** Recommandé / À explorer.

---

*(Cette liste sera mise à jour au fur et à mesure de l'évolution du projet et des besoins.)*
