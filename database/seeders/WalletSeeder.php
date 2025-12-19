<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            // Check if user already has a wallet
            if (!$user->wallet) {
                $user->wallet()->create([
                    'balance' => rand(1000, 50000),
                ]);
            }
        }
    }
}
