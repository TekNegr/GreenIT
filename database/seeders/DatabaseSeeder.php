<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
=======
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
>>>>>>> 3e757375a0cecffcd3fd974d0173cfa68ba026da
    }
}
