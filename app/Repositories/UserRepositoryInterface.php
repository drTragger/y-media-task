<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $userData): User;
    public function getUserById(int $id): ?User;
    public function getUserByEmail(string $email): ?User;
    public function update(int $userId, array $data): bool;
}
