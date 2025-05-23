<?php

namespace Database\Seeders;

use App\Models\Docter;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'docter1',
            'email' => 'docter1@gmail.com',
            'phone' => '0822141454',
            'password' => 'docter1password',
            'role' => 'DOCTER'
        ]);
        Docter::create([
            'user_id' => $user->id,
            'specialization' => 'Bedah mata'
        ]);
    }
}
