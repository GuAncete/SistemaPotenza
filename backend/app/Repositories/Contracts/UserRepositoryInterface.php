<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function findById(int $id): ?User;
}
