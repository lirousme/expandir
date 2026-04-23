<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class User
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $username,
        private readonly string $passwordHash
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }
}
