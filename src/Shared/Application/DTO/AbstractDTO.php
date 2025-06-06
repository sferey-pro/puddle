<?php

declare(strict_types=1);

namespace App\Shared\Application\DTO;

use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractDTO
{
    private ConstraintViolationListInterface $errors;

    public function setErrors(ConstraintViolationListInterface $errors): void
    {
        $this->errors = $errors;
    }

    public function hasErrors(): bool
    {
        return isset($this->errors) && \count($this->errors) > 0;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();
        }

        return $errors;
    }
}
