<?php

namespace Kernel\Infrastructure\Persistence\Repository;

final class QueryBuilder
{
    private array $criteria = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $joins = [];
    
    public static function create(): self
    {
        return new self();
    }
    
    // ========== CRITÈRES ==========
    
    public function where(string $field, mixed $value): self
    {
        $this->criteria[$field] = $value;
        return $this;
    }
    
    public function whereIn(string $field, array $values): self
    {
        $this->criteria[$field] = ['IN' => $values];
        return $this;
    }
    
    public function whereBetween(string $field, mixed $start, mixed $end): self
    {
        $this->criteria[$field] = ['BETWEEN' => [$start, $end]];
        return $this;
    }
    
    public function whereGreaterThan(string $field, mixed $value): self
    {
        $this->criteria[$field] = ['>' => $value];
        return $this;
    }
    
    public function whereLike(string $field, string $pattern): self
    {
        $this->criteria[$field] = ['LIKE' => $pattern];
        return $this;
    }
    
    // ========== TRI ==========
    
    public function orderBy(string $field, string $direction = 'ASC'): self
    {
        $this->orderBy[$field] = $direction;
        return $this;
    }
    
    // ========== PAGINATION ==========
    
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }
    
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }
    
    // ========== JOINS ==========
    
    public function join(string $relation, ?string $alias = null): self
    {
        $this->joins[] = ['type' => 'INNER', 'relation' => $relation, 'alias' => $alias];
        return $this;
    }
    
    public function leftJoin(string $relation, ?string $alias = null): self
    {
        $this->joins[] = ['type' => 'LEFT', 'relation' => $relation, 'alias' => $alias];
        return $this;
    }
    
    // ========== GETTERS ==========
    
    public function getCriteria(): array { return $this->criteria; }
    public function getOrderBy(): array { return $this->orderBy; }
    public function getLimit(): ?int { return $this->limit; }
    public function getOffset(): ?int { return $this->offset; }
    public function getJoins(): array { return $this->joins; }
}