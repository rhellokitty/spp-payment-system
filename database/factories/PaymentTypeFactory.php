<?php

namespace Database\Factories;

use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaymentType>
 */
class PaymentTypeFactory extends Factory
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
            'name' => $this->faker->randomElement(['Pembayaran SPP', 'Pembayaran buku', 'Pembayaran Wisuda', 'Pembayaran Gedung']),
            'due_day' => $this->faker->numberBetween(1, 30),
            'amount' => $this->faker->numberBetween(100000, 1000000),
            'is_recurring' => $this->faker->boolean(false),
        ];
    }
}
