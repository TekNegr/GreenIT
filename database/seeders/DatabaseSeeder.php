<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed departments
        DB::table('departements')->insert([
            ['code' => '75', 'created_at' => now(), 'updated_at' => now()],
            // Add other departments as needed
        ]);

        // Add other seed data here
    }
}
