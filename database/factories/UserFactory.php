<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'role' => 'agent',
            'service_id' => null,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'name' => 'Admin Super',
            'email' => 'admin@example.com',
            'role' => 'super_admin',
        ]);
    }
}
