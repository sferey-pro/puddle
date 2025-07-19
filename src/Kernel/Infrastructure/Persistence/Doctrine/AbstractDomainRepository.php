<?php

namespace Kernel\Infrastructure\Persistence\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Kernel\Domain\Repository\RepositoryInterface;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Kernel\Infrastructure\Persistence\EntityNotFoundException;
use Kernel\Infrastructure\Persistence\Repository\QueryBuilder;

abstract class AbstractDomainRepository extends ServiceEntityRepository implements RepositoryInterface
{
    // ========== MÉTHODES CONVENTIONNÉES ==========
    
    public function find($id, $lockMode = null, $lockVersion = null): ?object
    {
        return parent::find($id, $lockMode, $lockVersion);
    }
    
    public function get(mixed $id): object
    {
        $entity = $this->find($id);
        if (null === $entity) {
            throw EntityNotFoundException::fromId($id, $this->getClassName());
        }
        return $entity;
    }
    
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        return parent::findOneBy($criteria, $orderBy);
    }
    
    public function getBy(array $criteria): object
    {
        $entity = $this->findOneBy($criteria);
        if (null === $entity) {
            throw EntityNotFoundException::fromCriteria($criteria, $this->getClassName());
        }
        return $entity;
    }
    
    public function fetchAll(): array
    {
        return $this->findAll();
    }
    
    public function fetchBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }
    
    public function exists(mixed $id): bool
    {
        return null !== $this->find($id);
    }
    
    public function existsBy(array $criteria): bool
    {
        return $this->count($criteria) > 0;
    }
    
    // ========== QUERY BUILDER ==========
    
    public function query(QueryBuilder $queryBuilder): array
    {
        // On crée le VRAI QueryBuilder de Doctrine
        $doctrineQb = $this->createQueryBuilder($this->getAlias());
        
        // On traduit nos critères en syntaxe Doctrine
        foreach ($queryBuilder->getCriteria() as $field => $value) {
            $this->applyCriterion($doctrineQb, $field, $value);
        }
        
        // On traduit nos joins
        foreach ($queryBuilder->getJoins() as $join) {
            $method = strtolower($join['type']) . 'Join';
            $doctrineQb->$method(
                $this->getAlias() . '.' . $join['relation'], 
                $join['alias'] ?? null
            );
        }
        
        // On traduit le tri
        foreach ($queryBuilder->getOrderBy() as $field => $direction) {
            $doctrineQb->addOrderBy($this->getAlias() . '.' . $field, $direction);
        }
        
        // On applique la pagination
        if ($queryBuilder->getLimit() !== null) {
            $doctrineQb->setMaxResults($queryBuilder->getLimit());
        }
        if ($queryBuilder->getOffset() !== null) {
            $doctrineQb->setFirstResult($queryBuilder->getOffset());
        }
        
        return $doctrineQb->getQuery()->getResult();
    }
    
    public function queryOne(QueryBuilder $queryBuilder): ?object
    {
        $results = $this->query($queryBuilder->limit(1));
        return $results[0] ?? null;
    }
    
    protected function applyCriterion(
        DoctrineQueryBuilder $doctrineQb,
        string $field, 
        mixed $value
    ): void {
        $alias = $this->getAlias();
        $paramName = str_replace('.', '_', $field);
        
        if (is_array($value)) {
            $operator = array_key_first($value);
            $operand = $value[$operator];
            
            match ($operator) {
                'IN' => $doctrineQb
                    ->andWhere($doctrineQb->expr()->in("$alias.$field", ":$paramName"))
                    ->setParameter($paramName, $operand),
                    
                'BETWEEN' => $doctrineQb
                    ->andWhere($doctrineQb->expr()->between(
                        "$alias.$field", 
                        ":{$paramName}_start", 
                        ":{$paramName}_end"
                    ))
                    ->setParameter("{$paramName}_start", $operand[0])
                    ->setParameter("{$paramName}_end", $operand[1]),
                    
                '>' => $doctrineQb
                    ->andWhere($doctrineQb->expr()->gt("$alias.$field", ":$paramName"))
                    ->setParameter($paramName, $operand),
                    
                '<' => $doctrineQb
                    ->andWhere($doctrineQb->expr()->lt("$alias.$field", ":$paramName"))
                    ->setParameter($paramName, $operand),
                    
                'LIKE' => $doctrineQb
                    ->andWhere($doctrineQb->expr()->like("$alias.$field", ":$paramName"))
                    ->setParameter($paramName, $operand),
                    
                'NOT' => $doctrineQb
                    ->andWhere($doctrineQb->expr()->neq("$alias.$field", ":$paramName"))
                    ->setParameter($paramName, $operand),
                    
                default => throw new \InvalidArgumentException("Unknown operator: $operator")
            };
        } else {
            // Égalité simple
            $doctrineQb
                ->andWhere("$alias.$field = :$paramName")
                ->setParameter($paramName, $value);
        }
    }
    
    abstract protected function getAlias(): string;
}