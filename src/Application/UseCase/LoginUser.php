<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use DomainException;

final class LoginUser
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function execute(string $username, string $password): User
    {
        $user = $this->users->findByUsername($username);

        if ($user === null || !password_verify($password, $user->passwordHash())) {
            throw new DomainException('Credenciais inválidas.');
        }

        return $user;
    }
}
