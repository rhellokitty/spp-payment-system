<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'birth_date' => fake()->date(),
            'parent_name' => fake()->name(),
            'parent_phone_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'gender' => fake()->randomElement(['male', 'female']),
            'status' => fake()->randomElement(['active', 'graduated', 'transferred', 'dropped_out']),
        ];
    }
}
