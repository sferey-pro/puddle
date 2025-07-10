<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Service\Otp;

use App\Auth\Domain\Otp\OtpGeneratorInterface;

/**
 * Générateur d'OTP sécurisé utilisant les fonctions cryptographiques de PHP.
 */
final class SecureOtpGenerator implements OtpGeneratorInterface
{
    private const OTP_LENGTH = 6;

    public function generate(): string
    {
        try {
            // N'utilisez JAMAIS rand() ou mt_rand() pour des besoins cryptographiques.
            // random_int() génère des entiers cryptographiquement sécurisés.
            $min = 10 ** (self::OTP_LENGTH - 1); // 100000
            $max = (10 ** self::OTP_LENGTH) - 1;   // 999999

            $code = random_int($min, $max);

            return (string) $code;
        } catch (\Exception $e) {
            // Si les sources d'aléa du système d'exploitation sont épuisées. Très rare.
            throw new \RuntimeException('Failed to generate a cryptographically secure OTP.', 0, $e);
        }
    }
}
