<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nik = '351420310803';
        for ($i = 10000; $i < 10050; $i++) {
            Patient::create([
                'name' => fake()->name(),
                'nik' => $nik . $i,
                'birthday' => fake()->date(),
                'gender' => fake()->randomElement(['male', 'female']),
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'emergency_phone' => fake()->phoneNumber()
            ]);
        }
    }
}
