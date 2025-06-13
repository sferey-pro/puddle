## Domain Event

Ajouter : 
    public static function eventName(): string
    {
        return 'sales.order.created';
    }


## Amélioration Context IA

AggregateRootId sont écrit comme ceci : 

final class UserId implements \Stringable
{
    use AggregateRootId;
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
