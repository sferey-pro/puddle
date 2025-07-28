<?php

declare(strict_types=1);

namespace Authentication\Domain\Model;

use Authentication\Domain\ValueObject\IpAddress;

/**
 * Représente une adresse IP bloquée pour des raisons de sécurité.
 *
 * Cette entité suit les tentatives de connexion suspectes et permet
 * de bloquer temporairement ou définitivement des adresses IP.
 */
final class BlockedIP
{
    private string $ipAddress;
    private string $reason;
    private \DateTimeImmutable $blockedAt;
    private ?\DateTimeImmutable $expiresAt;
    private string $createdBy;

    private function __construct(
        string $ipAddress,
        string $reason,
        \DateTimeImmutable $blockedAt,
        ?\DateTimeImmutable $expiresAt,
        string $createdBy
    ) {
        $this->ipAddress = $ipAddress;
        $this->reason = $reason;
        $this->blockedAt = $blockedAt;
        $this->expiresAt = $expiresAt;
        $this->createdBy = $createdBy;
    }

    /**
     * Bloque une IP pour une durée déterminée.
     */
    public static function blockTemporarily(
        string $ipAddress,
        string $reason,
        \DateInterval $duration,
        string $blockedBy = 'system'
    ): self {
        $now = new \DateTimeImmutable();

        return new self(
            ipAddress: $ipAddress,
            reason: $reason,
            blockedAt: $now,
            expiresAt: $now->add($duration),
            createdBy: $blockedBy
        );
    }

    /**
     * Bloque une IP de manière permanente.
     */
    public static function blockPermanently(
        string $ipAddress,
        string $reason,
        string $blockedBy = 'system'
    ): self {
        return new self(
            ipAddress: $ipAddress,
            reason: $reason,
            blockedAt: new \DateTimeImmutable(),
            expiresAt: null,
            createdBy: $blockedBy
        );
    }

    /**
     * Crée un blocage automatique suite à trop de tentatives échouées.
     */
    public static function fromFailedAttempts(
        string $ipAddress,
        int $attemptCount,
        \DateInterval $blockDuration
    ): self {
        return self::blockTemporarily(
            ipAddress: $ipAddress,
            reason: sprintf('Automated block: %d failed login attempts', $attemptCount),
            duration: $blockDuration,
            blockedBy: 'system'
        );
    }

    /**
     * Vérifie si le blocage est toujours actif.
     */
    public function isActive(\DateTimeImmutable $now = new \DateTimeImmutable()): bool
    {
        if ($this->isPermanent()) {
            return true;
        }

        return $this->expiresAt > $now;
    }

    /**
     * Vérifie si le blocage est permanent.
     */
    public function isPermanent(): bool
    {
        return $this->expiresAt === null;
    }

    /**
     * Prolonge un blocage temporaire.
     */
    public function extend(\DateInterval $additionalDuration): void
    {
        if ($this->isPermanent()) {
            throw new \LogicException('Cannot extend a permanent block');
        }

        $this->expiresAt = $this->expiresAt->add($additionalDuration);
    }

    /**
     * Convertit un blocage temporaire en blocage permanent.
     */
    public function makePermanent(string $updatedBy): void
    {
        if ($this->isPermanent()) {
            return;
        }

        $this->expiresAt = null;
        $this->reason .= sprintf(' (Made permanent by %s on %s)',
            $updatedBy,
            (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        );
    }

    // ========== GETTERS ==========

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getBlockedAt(): \DateTimeImmutable
    {
        return $this->blockedAt;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    /**
     * Retourne le temps restant avant expiration.
     */
    public function getRemainingTime(\DateTimeImmutable $now = new \DateTimeImmutable()): ?\DateInterval
    {
        if ($this->isPermanent() || !$this->isActive($now)) {
            return null;
        }

        return $now->diff($this->expiresAt);
    }
}
