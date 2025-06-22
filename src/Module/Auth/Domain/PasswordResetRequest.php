<?php

namespace App\Module\Auth\Domain;

use App\Module\Auth\Domain\Event\PasswordResetRequested;
use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;
use App\Module\Auth\Domain\ValueObject\HashedToken; // Nous allons créer ce VO
use App\Module\Auth\Domain\ValueObject\IpAddress;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\DomainEventTrait;
use DateTimeImmutable;

/**
 * Représente une demande de réinitialisation de mot de passe.
 * C'est un agrégat qui garantit que chaque demande est unique,
 * a une durée de vie limitée et est associée à un utilisateur.
 */
final class PasswordResetRequest extends AggregateRoot
{
    use DomainEventTrait;

    private readonly PasswordResetRequestId $id;

    private \DateTimeImmutable $expiresAt;
    private bool $used = false;

    private ?UserId $userId;
    private ?string $selector;
    private ?HashedToken $hashedToken;

    private IpAddress $ipAddress;
    private Email $requestedEmail;

    /**
     * Crée une nouvelle demande de réinitialisation de mot de passe.
     * Enregistre un événement de domaine pour signaler cette création.
     */
    private function __construct(
        Email $requestedEmail,
        IpAddress $ipAddress,
        \DateTimeImmutable $expiresAt,
        ?UserId $userId = null,
        ?string $selector = null,
        ?HashedToken $hashedToken = null
    ) {
        $this->id = PasswordResetRequestId::generate();

        $this->requestedEmail = $requestedEmail;
        $this->ipAddress = $ipAddress;
        $this->expiresAt = $expiresAt;
        $this->userId = $userId;
        $this->selector = $selector;
        $this->hashedToken = $hashedToken;
    }

    /**
     * Factory pour une demande réelle d'un utilisateur connu.
     */
    public static function createForRealUser(
        UserId $userId,
        Email $requestedEmail,
        IpAddress $ipAddress,
        \DateTimeImmutable $expiresAt,
        string $selector,
        HashedToken $hashedToken,
        string $publicTokenForEmail
    ): self {
        $request = new self($requestedEmail, $ipAddress, $expiresAt, $userId, $selector, $hashedToken);

        $request->recordDomainEvent(new PasswordResetRequested(
            $request->id(),
            $userId,
            $requestedEmail,
            $expiresAt,
            $publicTokenForEmail
        ));

        return $request;
    }

    /**
     * Factory pour logger une tentative sur un utilisateur inconnu.
     */
    public static function logAttemptForUnknownUser(
        Email $requestedEmail,
        IpAddress $ipAddress,
        \DateTimeImmutable $expiresAt
    ): self {

        return new self($requestedEmail, $ipAddress, $expiresAt);
    }

    /**
     * Vérifie si la demande a expiré.
     */
    public function isExpired(\DateTimeImmutable $now): bool
    {
        return $this->expiresAt < $now;
    }

    /**
     * Vérifie si le token a déjà été utilisé.
     */
    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * Marque le token comme utilisé pour empêcher sa réutilisation.
     */
    public function markAsUsed(): void
    {
        $this->used = true;
    }

    // --- Accesseurs ---
    public function id(): PasswordResetRequestId
    {
        return $this->id;
    }

    public function userId(): ?UserId
    {
        return $this->userId;
    }

    public function selector(): ?string
    {
        return $this->selector;
    }

    public function hashedToken(): ?HashedToken
    {
        return $this->hashedToken;
    }

    public function expiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function requestedEmail(): Email
    {
        return $this->requestedEmail;
    }

    public function ipAddress(): IpAddress
    {
        return $this->ipAddress;
    }
}
