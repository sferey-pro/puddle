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


## Recherche de la logique 

Faire la recherche entre Shared et SharedContext

De même est-ce que les VO classique comme Email / Username / Address ne devrait pas être dans Shared au lieu de Module/SharedContext

Faire la recherche entre Core et SharedContext ou Shared

