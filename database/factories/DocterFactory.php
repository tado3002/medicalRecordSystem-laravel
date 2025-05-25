<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Docter>
 */
class DocterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->text(20),
            'role' => 'DOCTER',
            'phone' => fake()->phoneNumber(),
        ]);
        return [
            'user_id' => $user->id,
            'specialization' => fake()->randomElement([
                'THT',
                'Bedah saraf',
                'Neurologi',
                'Dokter Umum',
                'Dokter bayi',
                'Jantung'
            ])
        ];
    }
}
