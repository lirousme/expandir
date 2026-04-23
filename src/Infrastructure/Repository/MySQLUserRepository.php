<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Support\Env;
use DateTimeImmutable;
use DateTimeZone;
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
        $timezone = Env::get('APP_TIMEZONE', 'America/Sao_Paulo') ?? 'America/Sao_Paulo';
        $createdAt = (new DateTimeImmutable('now', new DateTimeZone($timezone)))->format('Y-m-d H:i:s');

        $statement = $this->connection->prepare('INSERT INTO users (username, password_hash, created_at) VALUES (:username, :password_hash, :created_at)');
        $statement->execute([
            'username' => $user->username(),
            'password_hash' => $user->passwordHash(),
            'created_at' => $createdAt,
        ]);

        return new User((int) $this->connection->lastInsertId(), $user->username(), $user->passwordHash());
    }
}
