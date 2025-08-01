<?php

namespace Authentication\Application\Query\DTO;

final readonly class CredentialListDTO
{
    /**
     * @param CredentialDTO[] $credentials
     */
    public function __construct(
        public array $credentials,
        public int $total
    ) {}
}
