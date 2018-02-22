<?php declare(strict_types=1);

namespace SocialNews\User\Domain;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class User
{
    private $id;
    private $username;
    private $passwordHash;
    private $createdAt;
    private $failedLoginAttempts;
    private $lastFailedLoginAttempt;

    private $recordedEvents = [];

    public function __construct(
        UuidInterface $id,
        string $username,
        string $passwordHash,
        DateTimeImmutable $createdAt,
        int $failedLoginAttempts,
        ?DateTimeImmutable $lastFailedLoginAttempt
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->createdAt = $createdAt;
        $this->failedLoginAttempts = $failedLoginAttempts;
        $this->lastFailedLoginAttempt = $lastFailedLoginAttempt;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getFailedLoginAttempts(): int
    {
        return $this->failedLoginAttempts;
    }

    public function getLastFailedLoginAttempt(): ?DateTimeImmutable
    {
        return $this->lastFailedLoginAttempt;
    }

    public function getRecordedEvents(): array
    {
        return $this->recordedEvents;
    }

    public function clearRecordedEvents(): void
    {
        $this->recordedEvents = [];
    }

    public static function register(string $username, string $password): User
    {
        return new User(
            Uuid::uuid4(),
            $username,
            password_hash($password, PASSWORD_DEFAULT),
            new DateTimeImmutable(),
            0,
            null
        );
    }

    public function logIn(string $password): void
    {
        if (!password_verify($password, $this->passwordHash)) {
            $this->lastFailedLoginAttempt = new DateTimeImmutable();
            $this>$this->failedLoginAttempts++;
            return;
        }
        $this->failedLoginAttempts = 0;
        $this->lastFailedLoginAttempt = null;

        $this->recordedEvents[] = new UserWasLoggedIn();
    }
}
