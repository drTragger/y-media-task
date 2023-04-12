<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Mail\SentMessage;

interface UserInterface
{
    public function register(array $data): User;
    public function getUserById(int $id): ?User;
    public function login(string $email, string $password): array;
    public function sendPasswordResetToken(User $user): ?SentMessage;
    public function resetPassword(string $token, string $password): void;
}
