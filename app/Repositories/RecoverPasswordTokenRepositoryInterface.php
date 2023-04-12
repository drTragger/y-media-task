<?php

namespace App\Repositories;

use App\Models\RecoverPasswordToken;

interface RecoverPasswordTokenRepositoryInterface
{
    public function store(int $userId, string $token): RecoverPasswordToken;
    public function get(string $token): ?RecoverPasswordToken;
    public function setIsUsed(RecoverPasswordToken $token): bool;
}
