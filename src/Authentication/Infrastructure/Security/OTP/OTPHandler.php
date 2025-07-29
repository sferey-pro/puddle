<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security\OTP;

use Authentication\Domain\Exception\InvalidOTPException;
use Authentication\Domain\Model\AccessCredential\OTPCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\Service\TokenGeneratorInterface;
use Authentication\Domain\ValueObject\Token\OTPCode;
use Identity\Domain\ValueObject\Identifier;
use InvalidArgumentException;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Gestionnaire principal des codes OTP.
 *
 * RESPONSABILITÉS :
 * - Générer des codes sécurisés
 * - Persister les credentials
 * - Vérifier et consommer les codes
 */
final class OTPHandler implements OTPHandlerInterface
{
    private const OTP_LENGTH = 6;
    private const DEFAULT_VALIDITY_MINUTES = 10;
    private const DEFAULT_MAX_ATTEMPTS = 3;

    public function __construct(
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        #[Autowire('%kernel.secret%')]
        private readonly string $secret // Pour signature HMAC
    ) {
        if (!$secret) {
            throw new InvalidArgumentException('A non-empty secret is required.');
        }
    }

    public function createOTP(
        Identifier $identifier,
        UserId $userId,
        array $metadata = [],
        ?\DateTimeImmutable $expiresAt = null
    ): OTPDetails {
        // Générer le code
        $code = OTPCode::generate(self::OTP_LENGTH, self::DEFAULT_VALIDITY_MINUTES);

        // Créer les détails
        $otpDetails = OTPDetails::create(
            code: $code,
            identifier: $identifier,
            userId: $userId,
            expiresAt: $code->expiresAt(),
            maxAttempts: self::DEFAULT_MAX_ATTEMPTS,
            metadata: $metadata
        );

        // Générer une signature pour sécurité additionnelle
        $signature = $this->generateSignature($otpDetails);
        $otpDetails = $otpDetails->withSignature($signature);

        // Créer et persister le credential
        $credential = OTPCredential::fromDetails($otpDetails);
        $this->credentialRepository->save($credential);

        return $otpDetails;
    }

    public function consumeOTP(
        Identifier $identifier,
        string $code
    ): OTPDetails {
        // Récupérer le credential le plus récent
        $credential = $this->credentialRepository->findLatestByIdentifier($identifier);

        if (!$credential || !$credential instanceof OTPCredential) {
            throw InvalidOTPException::notFound();
        }

        // Vérifier l'expiration
        if ($credential->isExpired()) {
            throw InvalidOTPException::expired();
        }

        // Vérifier si déjà utilisé
        if ($credential->isUsed()) {
            throw InvalidOTPException::alreadyUsed();
        }

        // Vérifier le nombre de tentatives
        if ($credential->getAttempts() >= self::DEFAULT_MAX_ATTEMPTS) {
            throw InvalidOTPException::tooManyAttempts();
        }

        // Vérifier le code
        if (!$credential->verifyCode($code)) {
            $credential->incrementAttempts();
            $this->credentialRepository->save($credential);

            $attemptsLeft = self::DEFAULT_MAX_ATTEMPTS - $credential->getAttempts();
            throw InvalidOTPException::incorrect($attemptsLeft);
        }

        // Marquer comme utilisé
        $credential->markAsUsed();
        $this->credentialRepository->save($credential);

        // Retourner les détails
        return OTPDetails::fromHash(
            hashedCode: $credential->getHashedCode(),
            identifier: $identifier,
            userId: $credential->getUserId(),
            expiresAt: $credential->getExpiresAt(),
            maxAttempts: self::DEFAULT_MAX_ATTEMPTS,
            metadata: $credential->getMetadata(),
            signature: $credential->getSignature()
        );
    }

    public function verifyOTP(
        Identifier $identifier,
        string $code
    ): bool {
        try {
            $credential = $this->credentialRepository->findLatestByIdentifier($identifier);

            if (!$credential || !$credential instanceof OTPCredential) {
                return false;
            }

            return !$credential->isExpired()
                && !$credential->isUsed()
                && $credential->verifyCode($code);

        } catch (\Exception) {
            return false;
        }
    }

    private function generateSignature(OTPDetails $details): string
    {
        $data = sprintf(
            '%s:%s:%s',
            $details->getIdentifier()->value(),
            $details->getUserId()->toString(),
            $details->getExpiresAt()->format('c')
        );

        return strtr(substr(base64_encode(hash_hmac('sha256', $data, $this->secret, true)), 0, 8), '/+', '._');
    }
}
