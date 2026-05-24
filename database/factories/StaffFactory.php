<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'admin' => 0,
            'role' => 2,
            'active' => 1,
            'datecreated' => now(),
            'default_language' => 'english',
            'direction' => 'ltr',
        ];
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'admin' => 1,
            'role' => 1,
            'email' => 'admin@crm.com',
        ]);
    }
}
