<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Support\Env;
use DomainException;

final class RegisterUser
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function execute(string $username, string $password): User
    {
        if ($this->users->findByUsername($username) !== null) {
            throw new DomainException('Nome de usuário já está em uso.');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, [
            'cost' => (int) Env::get('PASSWORD_BCRYPT_COST', '10'),
        ]);

        if ($hash === false) {
            throw new DomainException('Não foi possível criptografar a senha.');
        }

        return $this->users->create(new User(null, $username, $hash));
    }
}
