<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['deposit', 'withdrawal', 'transfer', 'payment'];

        foreach ($types as $type) {
            TransactionType::firstOrCreate(['title' => $type]);
        }
    }
}
