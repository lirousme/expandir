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
        $statement = $this->connection->prepare('SELECT id, username, password_hash, created_at FROM users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        $row = $statement->fetch();

        if (!$row) {
            return null;
        }

        return new User(
            (int) $row['id'],
            (string) $row['username'],
            (string) $row['password_hash'],
            isset($row['created_at']) ? (string) $row['created_at'] : null
        );
    }

    public function create(User $user): User
    {
        $statement = $this->connection->prepare(
            "INSERT INTO users (username, password_hash, created_at)
             VALUES (:username, :password_hash, CONVERT_TZ(UTC_TIMESTAMP(), '+00:00', '-03:00'))"
        );
        $statement->execute([
            'username' => $user->username(),
            'password_hash' => $user->passwordHash(),
        ]);

        $userId = (int) $this->connection->lastInsertId();
        $createdAt = null;

        $timestampStatement = $this->connection->prepare('SELECT created_at FROM users WHERE id = :id LIMIT 1');
        $timestampStatement->execute(['id' => $userId]);
        $timestamp = $timestampStatement->fetchColumn();

        if (is_string($timestamp)) {
            $createdAt = $timestamp;
        }

        error_log(sprintf('[timezone-debug] created_at persisted (America/Sao_Paulo): %s', $createdAt ?? 'null'));

        return new User($userId, $user->username(), $user->passwordHash(), $createdAt);
    }
}
