<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * @inheritDoc
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'user_id' => $user->id,
            'title' => $this->faker->title,
            'phone' => $this->faker->unique()->phoneNumber,
            'description' => $this->faker->text
        ];
    }
}
