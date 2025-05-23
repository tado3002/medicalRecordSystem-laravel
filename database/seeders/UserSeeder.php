<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'phone' => '0822141454',
            'password' => 'testtesttest',
            'role' => 'ADMIN'
        ]);
    }
}
