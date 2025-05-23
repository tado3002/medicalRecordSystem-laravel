<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Docter;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 10; $i <= 30; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => "tests.$i@gmail.com",
                'password' => "testingtesting$i",
                'role' => 'DOCTER',
                'phone' => fake()->phoneNumber()
            ]);

            $docter = Docter::create([
                'user_id' => $user->id,
                'specialization' => fake()->randomElement([
                    'THT',
                    'Bedah saraf',
                    'Neurologi',
                    'Dokter Umum',
                    'Dokter bayi',
                    'Jantung'
                ])
            ]);

            $nik = '35142031080300';
            $patient = Patient::create([
                'name' => fake()->name(),
                'nik' => $nik . $i,
                'birthday' => fake()->date(),
                'gender' => fake()->randomElement(['male', 'female']),
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'emergency_phone' => fake()->phoneNumber()
            ]);

            Appointment::create([
                'docter_id' => $docter->id,
                'patient_id' => $patient->id,
                'date' => fake()->date(),
                'notes' => fake()->text(40),
                'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled'])
            ]);
        }
    }
}
