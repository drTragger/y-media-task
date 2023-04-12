<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @param array $userData
     * @return User
     */
    public function create(array $userData): User
    {
        return User::create($userData);
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function update(int $userId, array $data): bool
    {
        return User::whereId($userId)->update($data);
    }
}
