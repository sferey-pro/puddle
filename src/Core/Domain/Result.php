<?php

declare(strict_types=1);


namespace App\Core\Domain;

/**
 * Représente le résultat d'une opération qui peut soit réussir (success)
 * soit échouer (failure). Il rend la gestion d'erreur explicite.
 *
 * @template TValue Le type de la valeur en cas de succès.
 */
final class Result
{
    /**
     * @param TValue|null $value La valeur en cas de succès.
     * @param \DomainException|null $error L'erreur en cas d'échec.
     */
    private function __construct(
        private ?object $value,
        private ?\DomainException $error
    ) {}

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function isFailure(): bool
    {
        return $this->error !== null;
    }

    /**
     * Crée un résultat de succès.
     *
     * @template TSuccessValue
     *
     * @param TSuccessValue $value
     * @return self<TSuccessValue>
     */
    public static function success(object $value): self
    {
        return new self($value, null);
    }

    /**
     * Crée un résultat d'échec.
     *
     * @template TFailureValue
     *
     * @param \DomainException $error
     * @return self<TFailureValue>
     */
    public static function failure(\DomainException $error): self
    {
        return new self(null, $error);
    }

    public function error(): ?\DomainException
    {
        return $this->error;
    }

    /**
     * @return TValue
     * @throws \LogicException Si on essaie d'accéder à la valeur d'un résultat en échec.
     */
    public function value(): object
    {
        if ($this->isFailure()) {
            throw new \LogicException('Cannot get value from a failure result.');
        }

        return $this->value;
    }
}
