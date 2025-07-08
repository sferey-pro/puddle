<?php

declare(strict_types=1);

namespace App\Core\Domain\Collection;

/**
 * Classe de base pour les collections d'objets dans le domaine.
 *
 * Fournit une implémentation de base pour l'itération et le comptage,
 * tout en garantissant l'immutabilité par défaut (pas de méthode add() publique ici).
 *
 * Les collections qui héritent de celle-ci doivent spécifier le type d'objet qu'elles contiennent.
 *
 * @template TValue Le type des éléments dans la collection.
 * @implements \IteratorAggregate<int, TValue>
 */
abstract class AbstractCollection implements \Countable, \IteratorAggregate
{
    /**
     * @param array<int, TValue> $items
     */
    public function __construct(protected array $items = [])
    {
    }

    /**
     * @return array<int, TValue>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @return TValue|null
     */
    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function filter(callable $callback): static
    {
        return new static(array_values(array_filter($this->items, $callback)));
    }
}
