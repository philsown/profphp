<?php declare(strict_types=1);

namespace SocialNews\User\Infrastructure;

use Doctrine\DBAL\Connection;
use SocialNews\User\Application\UsernameTakenQuery;

final class DbalUsernameTakenQuery implements UsernameTakenQuery
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function execute(string $username): bool
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('COUNT(*)');
        $qb->from('users');
        $qb->where("username = {$qb->createNamedParameter($username)}");

        $stmt = $qb->execute();
        return (bool) $stmt->fetchColumn();
    }
}
