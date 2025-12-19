<?php

namespace Database\Seeders;

use App\Models\TransactionStatus;
use Illuminate\Database\Seeder;

class TransactionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['pending', 'completed', 'failed', 'cancelled'];

        foreach ($statuses as $status) {
            TransactionStatus::firstOrCreate(['title' => $status]);
        }
    }
}
