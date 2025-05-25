<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'nik' => '351420310903' . fake()->randomNumber(3),
            'gender' => fake()->randomElement(['male', 'female']),
            'birthday' => fake()->date(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'emergency_phone' => fake()->phoneNumber()
        ];
    }
}
