<?php

declare(strict_types=1);

namespace App\Core\Domain\Validation;

/**
 * Un outil pour combiner plusieurs opérations de validation (Result)
 * et collecter toutes les erreurs ou retourner toutes les valeurs.
 */
final class ValidationResult
{
    /** @var \DomainException[] */
    private array $errors = [];

    /** @var object[] */
    private array $values = [];

    private function __construct() {}

    /**
     * @param array<string, Result> $results
     */
    public static function collect(array $results): self
    {
        $instance = new self();
        foreach ($results as $key => $result) {
            if ($result->isFailure()) {
                $instance->errors[] = $result->error;
            } else {
                $instance->values[$key] = $result->getValue();
            }
        }
        return $instance;
    }

    public function isSuccess(): bool
    {
        return empty($this->errors);
    }

    public function isFailure(): bool
    {
        return !$this->isSuccess();
    }

    /**
     * Retourne toutes les valeurs en cas de succès.
     * @return object[]
     */
    public function getValues(): array
    {
        if ($this->isFailure()) {
            throw new \LogicException('Cannot get values from a failed validation.');
        }
        return $this->values;
    }

    /**
     * Retourne une seule chaîne de caractères avec tous les messages d'erreur.
     */
    public function getJoinedErrorMessages(string $separator = "\n"): string
    {
        return implode($separator, array_map(fn($e) => $e->getMessage(), $this->errors));
    }
}
