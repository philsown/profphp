<?php declare(strict_types=1);

namespace SocialNews\User\Infrastructure;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Connection;
use SocialNews\User\Domain\User;
use SocialNews\User\Domain\UserRepository;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use SocialNews\User\Domain\UserWasLoggedIn;
use Symfony\Component\HttpFoundation\Session\Session;
use LogicException;

final class DbalUserRepository implements UserRepository
{
    private $connection;
    private $session;

    public function __construct(Connection $connection, Session $session)
    {
        $this->connection = $connection;
        $this->session = $session;
    }

    public function add(User $user): void
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->insert('users');
        $qb->values([
            'id' => $qb->createNamedParameter($user->getId()->toString()),
            'username' => $qb->createNamedParameter($user->getUsername()),
            'password_hash' => $qb->createNamedParameter($user->getPasswordHash()),
            'created_at' => $qb->createNamedParameter($user->getCreatedAt(), Type::DATETIME),
        ]);

        $qb->execute();
    }

    public function save(User $user): void
    {
        foreach ($user->getRecordedEvents() as $event) {
            if ($event instanceof UserWasLoggedIn) {
                $this->session->set('userId', $user->getId()->toString());
                continue;
            }
            throw new LogicException(get_class($event) . ' was not handled');
        }
        $user->clearRecordedEvents();

        $qb = $this->connection->createQueryBuilder();

        $qb->update('users');
        $qb->set('username', $qb->createNamedParameter($user->getUsername()));
        $qb->set('password_hash', $qb->createNamedParameter($user->getPasswordHash()));
        $qb->set('failed_login_attempts', $qb->createNamedParameter($user->getFailedLoginAttempts()));
        $qb->set('last_failed_login_attempt', $qb->createNamedParameter(
            $user->getLastFailedLoginAttempt(),
            Type::DATETIME
        ));

        $qb->execute();
    }

    public function findByUsername(string $username): ?User
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->addSelect('id');
        $qb->addSelect('username');
        $qb->addSelect('password_hash');
        $qb->addSelect('created_at');
        $qb->addSelect('failed_login_attempts');
        $qb->addSelect('last_failed_login_attempt');
        $qb->from('users');
        $qb->where("username = {$qb->createNamedParameter($username)}");

        $stmt = $qb->execute();
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->createUserFromRow($row);
    }

    private function createUserFromRow(array $row): ?User
    {
        if (!$row) {
            return null;
        }

        $lastFailedLoginAttempt = null;
        if ($row['last_failed_login_attempt']) {
            $lastFailedLoginAttempt = new DateTimeImmutable($row['last_failed_login_attempt']);
        }

        return new User(
            Uuid::fromString($row['id']),
            $row['username'],
            $row['password_hash'],
            new DateTimeImmutable($row['created_at']),
            (int) $row['failed_login_attempts'],
            $lastFailedLoginAttempt
        );
    }
}
