# 4. Couche Infrastructure

Cette couche contient les détails techniques (Base de données, APIs externes...).

### Persistance (Doctrine)

* **VOs à valeur unique :** Utiliser un **Type Doctrine Personnalisé** (ex: `UserIdType`) qui hérite d'une classe de base abstraite.
* **VOs composites :** Utiliser des **`Embeddable`**.

```xml
<embeddable name="App\SharedContext\Domain\ValueObject\Money">
    <field name="amount" type="integer" column="amount" />
    <field name="currency" type="string" length="3" column="currency" />
</embeddable>
```
