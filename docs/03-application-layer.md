# 3. Couche Application

Cette couche orchestre les actions. Elle ne contient pas de logique métier.

### Commandes, Queries & Handlers

* Utilisent les attributs `#[AsCommandHandler]`, `#[AsQueryHandler]`, `#[AsMessageHandler]`.
* Sont des classes `final`.
* Les **Handlers d'événements (Projecteurs)** sont la convention standard pour réagir aux événements de domaine. Ils sont préférés à `EventSubscriberInterface` pour la logique métier.
* Les Handlers doivent être **petits et dédiés** (SRP).

### Organisation des Projectors (Pattern Multi-Handler)

Pour garder la logique de projection cohérente, il est recommandé de regrouper toutes les méthodes qui mettent à jour un même `ReadModel` dans **une seule classe Projector**.

Cette classe agit comme un "Multi-Handler". Elle n'implémente plus `EventSubscriberInterface`, mais expose plusieurs méthodes de gestion, chacune annotée avec l'attribut `#[AsMessageHandler]`. Cette approche est notre standard pour les projecteurs.

**Avantages :**
* **Organisation :** Toute la logique de projection pour une vue (ex: `CostItemView`) est centralisée.
* **Cohérence :** Aligne tous nos projecteurs sur notre `EventBus` (Symfony Messenger).
* **Transition Naturelle :** Facilite la refactorisation des anciens `EventSubscriber`.

**Exemple avec `CostItemProjector` :**

```php
<?php
// src/Module/CostManagement/Application/Projector/CostItemProjector.php

namespace App\Module\CostManagement\Application\Projector;

use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

// Le Projector est une classe 'final' qui regroupe les logiques.
// Il n'implémente plus EventSubscriberInterface.
final readonly class CostItemProjector
{
    public function __construct(/* ...dépendances... */)
    {
    }

    // Le handler pour l'événement de création
    #[AsMessageHandler()]
    public function handleCostItemAdded(CostItemAdded $event): void
    {
        // ... logique de création de la vue ...
    }

    // Le handler pour l'événement de mise à jour
    #[AsMessageHandler()]
    public function handleCostItemDetailsUpdated(CostItemDetailsUpdated $event): void
    {
        // ... logique de mise à jour de la vue ...
    }

    // ... etc. pour les autres événements liés à CostItem.
}
```

### DTOs et ReadModels

Ce sont les seuls objets de l'application qui doivent être **plats** (composés de types scalaires).

* **DTOs :** Utilisés en entrée des `Commands`.
* **ReadModels :** Représentent nos vues optimisées pour la lecture.

### Stratégie de Validation

Notre stratégie de validation repose sur deux niveaux de défense complémentaires pour garantir la robustesse des messages transitant par les bus. 
 - Le premier niveau est une validation d'invariants au constructeur, utilisant webmozart/assert pour assurer l'intégrité structurelle de nos Commandes et Value Objects dès leur instanciation (approche "fail-fast"). 
 - Le second niveau, géré par le middleware de validation de Messenger, s'appuie sur les attributs du composant symfony/validator pour appliquer des règles métier plus complexes (ex: unicité en base de données) sur l'objet de commande dans son ensemble, avant même qu'il n'atteigne le handler. 

Cette double approche garantit que les Handlers ne reçoivent que des messages qui sont à la fois structurellement cohérents et conformes à toutes les règles métier définies.
