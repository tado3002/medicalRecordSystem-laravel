<?php

namespace Database\Factories;

use App\Models\Docter;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $docter = Docter::factory(1)->createOne();
        $patient = Patient::factory(1)->createOne();
        return [
            'docter_id' => $docter->getAttribute('id'),
            'patient_id' => $patient->getAttribute('id'),
            'date' => fake()->dateTime(),
            'diagnosis' => fake()->text(20),
            'treatment' => fake()->text(20),
        ];
    }
}
