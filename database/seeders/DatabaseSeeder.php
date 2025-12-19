<?php

namespace Database\Seeders;

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

        User::create([
            'firstname' => 'super',
            'lastname' => 'admin',
            'email' => 'petitho91@gmail.com',
            'password' => bcrypt('Password@123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
