<?php

namespace Kernel\Domain\Repository;

interface RepositoryInterface
{
    // FIND : Retourne une entité ou null
    public function find(mixed $id): ?object;
    public function findBy(array $criteria, ?array $orderBy = null): ?object;
    public function findOneBy(array $criteria): ?object;

    // GET : Retourne une entité ou throw une exception
    public function get(mixed $id): object;
    public function getBy(array $criteria): object;

    // FETCH : Retourne une collection
    public function fetchAll(): array;
    public function fetchBy(array $criteria, ?array $orderBy = null, ?int $limit = null): array;

    // COUNT : Retourne un nombre
    public function count(array $criteria = []): int;

    // EXISTS : Retourne un boolean
    public function exists(mixed $id): bool;
    public function existsBy(array $criteria): bool;
}
