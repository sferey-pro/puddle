<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Core\Domain\Aggregate\AggregateRoot;
use App\Core\Domain\Event\DomainEventTrait;
use App\Module\Auth\Domain\Event\PasswordResetRequested;
use App\Module\Auth\Domain\ValueObject\HashedToken;
use App\Module\Auth\Domain\ValueObject\IpAddress;
use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Représente le processus de demande de réinitialisation de mot de passe.
 *
 * Cet agrégat a une double responsabilité :
 * 1. Gérer le cycle de vie d'une demande de réinitialisation VALIDE pour un utilisateur connu (création, expiration, utilisation).
 * 2. Servir de journal d'audit en enregistrant TOUTES les tentatives de demande, y compris celles pour des
 * utilisateurs inconnus, à des fins de sécurité et de throttling (limitation des tentatives).
 *
 * L'état de l'objet (la présence ou non d'un userId et d'un token) détermine la nature de la demande.
 */
final class PasswordResetRequest extends AggregateRoot
{
    use DomainEventTrait;

    private(set) \DateTimeImmutable $createdAt;
    private(set) \DateTimeImmutable $updatedAt;

    private readonly PasswordResetRequestId $id;
    private \DateTimeImmutable $expiresAt;
    private bool $used = false;

    // Ces propriétés sont nullables car une demande peut être une simple trace
    // pour un utilisateur inconnu, sans token ni utilisateur associé.
    private ?UserId $userId;
    private ?string $selector;
    private ?HashedToken $hashedToken;

    // Ces propriétés sont toujours présentes pour tracer l'origine de la demande.
    private IpAddress $ipAddress;
    private EmailAddress $requestedEmail;

    /**
     * Crée une nouvelle demande de réinitialisation de mot de passe.
     * Enregistre un événement de domaine pour signaler cette création.
     */
    private function __construct(
        EmailAddress $requestedEmail,
        IpAddress $ipAddress,
        \DateTimeImmutable $expiresAt,
        ?UserId $userId = null,
        ?string $selector = null,
        ?HashedToken $hashedToken = null,
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
     * Crée une demande de réinitialisation complète et valide pour un utilisateur connu du système.
     * C'est le seul cas où un événement est publié pour déclencher l'envoi d'un e-mail.
     *
     * @return self une nouvelle instance de la demande, prête à être persistée
     */
    public static function createForRealUser(
        UserId $userId,
        EmailAddress $requestedEmail,
        IpAddress $ipAddress,
        \DateTimeImmutable $expiresAt,
        string $selector,
        HashedToken $hashedToken,
        string $publicTokenForEmail,
    ): self {
        $request = new self($requestedEmail, $ipAddress, $expiresAt, $userId, $selector, $hashedToken);

        // L'événement `PasswordResetRequested` notifie les autres parties de l'application
        // (comme le service d'envoi d'e-mails) qu'une action est requise.
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
     * Enregistre une tentative de réinitialisation pour un e-mail qui ne correspond à aucun utilisateur connu.
     * L'objectif est de conserver une trace (un log) à des fins de sécurité, sans générer de token ni envoyer d'e-mail.
     *
     * @return self une nouvelle instance représentant la tentative
     */
    public static function logAttemptForUnknownUser(
        EmailAddress $requestedEmail,
        IpAddress $ipAddress,
        \DateTimeImmutable $expiresAt,
    ): self {
        return new self($requestedEmail, $ipAddress, $expiresAt);
    }

    /**
     * Vérifie si la fenêtre de temps pour utiliser cette demande est dépassée.
     *
     * @param \DateTimeImmutable $now le moment actuel pour la comparaison
     *
     * @return bool vrai si la demande a expiré, faux sinon
     */
    public function isExpired(\DateTimeImmutable $now): bool
    {
        return $this->expiresAt < $now;
    }

    /**
     * Vérifie si cette demande a déjà été utilisée pour réinitialiser un mot de passe.
     * Une demande ne peut être utilisée qu'une seule fois.
     *
     * @return bool vrai si la demande a déjà été utilisée
     */
    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * Marque la demande comme utilisée, la rendant invalide pour de futures tentatives.
     * C'est une action métier qui garantit l'unicité d'utilisation du token.
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

    public function requestedEmail(): EmailAddress
    {
        return $this->requestedEmail;
    }

    public function ipAddress(): IpAddress
    {
        return $this->ipAddress;
    }
}
