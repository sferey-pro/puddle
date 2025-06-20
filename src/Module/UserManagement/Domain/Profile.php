<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Event\ProfileCreated;
use App\Module\UserManagement\Domain\ValueObject\DisplayName;
use App\Module\UserManagement\Domain\ValueObject\Username;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\DomainEventTrait;

final class Profile extends AggregateRoot
{
    use DomainEventTrait;

    private ?string $firstName = null;
    private ?string $lastName = null;
    private ?Username $username = null;
    private ?DisplayName $displayName = null;
    private ?\DateTimeImmutable $dateOfBirth = null;

    private function __construct(
        private readonly UserId $userId,
    ) {
    }

    public static function create(
        UserId $userId,
    ): self {
        $profile = new self($userId);
        $profile->recordDomainEvent(new ProfileCreated(
            $profile->userId()
        ));

        return $profile;
    }

    // public function updateProfile(
    //     Name $newFirstName,
    //     Name $newLastName,
    //     ?\DateTimeImmutable $newDateOfBirth = null,
    //     ?string $newAddress = null,
    // ): void {
    //     $changed = false;
    //     if (!$this->firstName->isEqualTo($newFirstName)) {
    //         $this->firstName = $newFirstName;
    //         $changed = true;
    //     }
    //     if (!$this->lastName->isEqualTo($newLastName)) {
    //         $this->lastName = $newLastName;
    //         $changed = true;
    //     }
    //     if ($this->dateOfBirth !== $newDateOfBirth
    //         && !($this->dateOfBirth instanceof \DateTimeImmutable && $newDateOfBirth instanceof \DateTimeImmutable && $this->dateOfBirth->eq($newDateOfBirth))) {
    //         $this->dateOfBirth = $newDateOfBirth;
    //         $changed = true;
    //     }
    //     if ($this->address !== $newAddress) {
    //         $this->address = $newAddress;
    //         $changed = true;
    //     }

    //     if ($changed) {
    //         $this->recordDomainEvent(new ProfileUpdated(
    //             userId: $this->userId(),
    //         ));
    //     }
    // }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function dateOfBirth(): ?\DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function fullName(): string
    {
        return \sprintf('%s %s', $this->firstName, $this->lastName);
    }
}
