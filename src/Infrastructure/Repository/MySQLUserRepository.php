<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use PDO;

final class MySQLUserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function findByUsername(string $username): ?User
    {
        $statement = $this->connection->prepare('SELECT id, username, password_hash FROM users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        $row = $statement->fetch();

        if (!$row) {
            return null;
        }

        return new User((int) $row['id'], (string) $row['username'], (string) $row['password_hash']);
    }

    public function create(User $user): User
    {
        $statement = $this->connection->prepare('INSERT INTO users (username, password_hash, created_at) VALUES (:username, :password_hash, NOW())');
        $statement->execute([
            'username' => $user->username(),
            'password_hash' => $user->passwordHash(),
        ]);

        return new User((int) $this->connection->lastInsertId(), $user->username(), $user->passwordHash());
    }
}
