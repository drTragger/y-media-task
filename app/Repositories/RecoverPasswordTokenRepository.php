<?php

namespace App\Repositories;

use App\Models\RecoverPasswordToken;

class RecoverPasswordTokenRepository implements RecoverPasswordTokenRepositoryInterface
{
    /**
     * @param int $userId
     * @param string $token
     * @return RecoverPasswordToken
     */
    public function store(int $userId, string $token): RecoverPasswordToken
    {
        return RecoverPasswordToken::create([
            'user_id' => $userId,
            'token' => $token,
            'is_used' => false
        ]);
    }

    /**
     * @param string $token
     * @return RecoverPasswordToken|null
     */
    public function get(string $token): ?RecoverPasswordToken
    {
        return RecoverPasswordToken::where('token', $token)->first();
    }

    /**
     * @param RecoverPasswordToken $token
     * @return bool
     */
    public function setIsUsed(RecoverPasswordToken $token): bool
    {
        return $token->update(['is_used' => true]);
    }
}
