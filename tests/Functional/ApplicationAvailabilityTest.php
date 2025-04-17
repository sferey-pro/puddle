<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;

#[CoversNothing]
class ApplicationAvailabilityTest extends WebTestCase
{
    use HasBrowser;

    public const LOCALE = 'en';

    #[DataProvider('urlProvider')]
    #[TestDox('Smoke Test your URLs')]
    public function testPageIsSuccessful(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public static function urlProvider(): \Generator
    {
        yield 'Homepage' => ['/'];
        yield 'Registration' => ['/register'];
        yield 'Login' => ['/login'];
    }
}
