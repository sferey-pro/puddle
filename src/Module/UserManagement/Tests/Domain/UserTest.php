<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Tests\Domain;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Event\UserCreated;
use App\Module\UserManagement\Domain\User;
use App\Shared\Domain\Service\FixedClock;
use App\Shared\Domain\Service\SystemTime;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    private const DEFAULT_UUID = '26d72a29-ea3a-335e-8b82-bcad0da4bcda';

    protected function tearDown(): void
    {
        parent::tearDown();
        SystemTime::reset();
    }

    public function testUserCreationRecordsEventWithFixedTime(): void
    {
        // ARRANGE: On fige le temps à une date connue
        $frozenTime = new \DateTimeImmutable('2025-06-15 10:30:00');
        SystemTime::set(new FixedClock($frozenTime));

        // ACT: On exécute la logique à tester
        $user = User::create(
            UserId::fromString(static::DEFAULT_UUID),
            new Email('test@example.com')
        );
        $events = $user->pullDomainEvents();

        // ASSERT: On vérifie les résultats
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserCreated::class, $events[0]);
        $this->assertEquals(static::DEFAULT_UUID, (string) $events[0]->aggregateId());
        $this->assertEquals($frozenTime, $events[0]->occurredOn());
    }
}
