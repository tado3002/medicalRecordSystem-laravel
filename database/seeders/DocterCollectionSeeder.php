<?php

namespace Database\Seeders;

use App\Models\Docter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocterCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Docter::factory(30)->create();
    }
}
