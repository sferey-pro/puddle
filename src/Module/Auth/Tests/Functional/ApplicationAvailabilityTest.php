<?php

declare(strict_types=1);

namespace App\Module\Auth\Tests\Functional;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @internal
 *
 * Classe de test pour vérifier la disponibilité des pages critiques (Smoke Test)
 * du module Auth.
 */
#[CoversNothing]
class ApplicationAvailabilityTest extends WebTestCase
{
    // Le trait ResetDatabase s'assure que la base de données est propre avant chaque test.
    use ResetDatabase;

    /**
     * Teste les URLs accessibles par n'importe qui (visiteurs non authentifiés).
     */
    #[DataProvider('publicUrlProvider')]
    #[TestDox('URL publique ($url) est accessible')]
    public function testPublicPageIsSuccessful(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }

    /**
     * Fournit les URLs publiques critiques.
     */
    public static function publicUrlProvider(): \Generator
    {
        yield 'Registration' => ['/register'];
        yield 'Login' => ['/login'];
        yield 'Login Link Page' => ['/login/link'];
    }
}
