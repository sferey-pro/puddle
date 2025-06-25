# Le Répertoire `Shared`

## Rôle et Philosophie

Le répertoire `Shared` a un rôle très spécifique et pragmatique : il héberge du **code réutilisable qui n'appartient PAS au domaine métier**.

Son focus principal est la **mutualisation de composants pour les couches `UI` et `Infrastructure`**.

Il ne faut surtout pas le confondre avec `Module/SharedContext`, qui, lui, est notre **Shared Kernel** et contient du code de **domaine** partagé (`UserId`, `Email`, etc.).

Pensez au répertoire `Shared` comme à la "boîte à outils des composants réutilisables" de notre interface utilisateur et de notre infrastructure technique.

---

## Contenu Autorisé

### 1. Couche `UI` (`Shared/UI`)

C'est l'usage le plus courant pour ce répertoire.

* **Composants Twig** (`UI/Twig/Components/`):
    * Logique pour les composants d'interface réutilisables (ex: `Alert.php`, `Badge.php`, `Modal.php`).
    * Tout ce qui est utilisé via `<twig:ux:... />`.
* **Types de Formulaires Génériques** (`UI/Form/`):
    * Formulaires réutilisables et non liés à un domaine précis (ex: `ItemsPerPageType.php`, `ConfirmationType.php`).
* **Contrôleurs Utilitaires** :
    * Contrôleurs pour des pages de test ou de styleguide, non destinés à la production.

### 2. Couche `Infrastructure` (`Shared/Infrastructure`)

Moins fréquent, mais peut contenir des aides ou des services d'infrastructure transverses qui ne sont pas assez fondamentaux pour être dans `Core`.

* **Helpers de Persistance** :
    * Traits ou services génériques pour Doctrine qui ne sont pas spécifiques à un domaine.
* **Clients API Génériques** :
    * Wrappers pour des APIs externes si leur usage est partagé par plusieurs modules de manière purement technique.

---

## À ne JAMAIS mettre ici :

* **Toute forme de logique ou de règle métier.**
* Des `ValueObjects` de domaine.
* Des entités ou des agrégats.
* Des Commandes, Queries, ou Événements de domaine. Si c'est partagé, c'est le rôle du `SharedContext`.
