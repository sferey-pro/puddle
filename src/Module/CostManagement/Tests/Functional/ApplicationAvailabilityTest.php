<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Tests\Functional;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Factory\UserAccountFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @internal
 *
 * Classe de test pour vérifier la disponibilité des pages critiques (Smoke Test)
 * du module CostManagement
 */
#[CoversNothing]
class ApplicationAvailabilityTest extends WebTestCase
{
    // Le trait ResetDatabase s'assure que la base de données est propre avant chaque test.
    use ResetDatabase;

    /**
     * Ce test vérifie que les pages du gestionnaire de coûts, qui nécessitent une authentification,
     * sont bien accessibles une fois qu'un utilisateur est connecté.
     */
    #[DataProvider('authenticatedUrlProvider')]
    #[TestDox('Smoke Test : L\'URL authentifiée "$url" est accessible')]
    public function testAuthenticatedPageIsSuccessful(string $url): void
    {
        $client = self::createClient();

        // Création d'un utilisateur de test avec le rôle administrateur.
        /** @var Proxy $userAccount */
        $userAccount = UserAccountFactory::createOne([
            'email' => 'admin@puddle.com',
            'roles' => ['ROLE_ADMIN'],
        ]);

        // Connexion en tant que cet utilisateur.
        $client->loginUser($userAccount->_real());

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
        yield 'Cost Management - List' => ['/admin/cost-items/'];
        // yield 'Cost Management - New' => ['/admin/cost-items/new'];

        // yield 'Recurring Cost - List' => ['/admin/recurring-cost/'];
        // yield 'Recurring Cost - New' => ['/admin/recurring-cost/new'];
    }
}
