<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use Database\Factories\StudentFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ClassRoom>
 */
class ClassRoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startYear = $this->faker->numberBetween(2020, 2024);

        return [
            'id' => Str::uuid(),
            'school_level' => 'SMP',
            'name' => $this->faker->randomElement(['A', 'B', 'C']),
            'grade' => $this->faker->randomElement(['7', '8', '9']),
            'start_year' => $startYear,
            'end_year' => $startYear + 3,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (ClassRoom $classRoom) {
            $studentCount = fake()->numberBetween(20, 35);

            UserFactory::new()->student()->count($studentCount)->create()->each(function ($user) use ($classRoom) {
                StudentFactory::new()->create([
                    'user_id' => $user->id,
                    'class_room_id' => $classRoom->id,
                    'status' => 'active',
                ]);
            });
        });
    }
}
