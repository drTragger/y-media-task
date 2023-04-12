<?php

namespace Database\Factories;

use App\Models\RecoverPasswordToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecoverPasswordTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RecoverPasswordToken::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'user_id' => $user->id,
            'token' => $this->faker->uuid,
            'is_used' => false,
        ];
    }
}
