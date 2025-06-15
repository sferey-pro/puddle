<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Tests\Functional;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Factory\UserAccountFactory;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @internal
 *
 * Classe de test pour vérifier la disponibilité des pages critiques (Smoke Test)
 * du module ProductCatalog.
 */
#[CoversNothing]
final class ApplicationAvailabilityTest extends WebTestCase
{
    use ResetDatabase;

    /**
     * Ce test vérifie que les pages du catalogue produit, qui nécessitent une authentification,
     * sont bien accessibles une fois qu'un utilisateur est connecté.
     */
    #[DataProvider('authenticatedUrlProvider')]
    #[TestDox('Smoke Test : L\'URL authentifiée "$url" est accessible')]
    public function testAuthenticatedPageIsSuccessful(string $url): void
    {
        $client = self::createClient();

        // Création d'un utilisateur de test avec le rôle administrateur.
        $user = UserAccountFactory::new()->create(['roles' => ['ROLE_ADMIN']]);

        // Connexion en tant que cet utilisateur.
        $client->loginUser($user);

        // Requête sur l'URL à tester.
        $client->request('GET', $url);

        // Assertion que la page a répondu avec un code de succès (2xx).
        self::assertResponseIsSuccessful();
    }

    /**
     * Fournit la liste des URLs à tester pour ce module.
     */
    public static function authenticatedUrlProvider(): \Generator
    {
        yield 'Liste des produits' => ['/admin/products/'];
        yield 'Création d’un produit' => ['/admin/products/new'];
    }
}
