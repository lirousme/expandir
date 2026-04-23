<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findByUsername(string $username): ?User;

    public function create(User $user): User;
}
