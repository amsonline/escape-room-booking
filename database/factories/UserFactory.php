<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'date_of_birth' => $this->faker->date,
        ];
    }

    public function withBirthday($birthday): UserFactory
    {
        return $this->state([
            'date_of_birth' => $birthday,
        ]);
    }
}
