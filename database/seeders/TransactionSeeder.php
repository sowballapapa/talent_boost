<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $types = TransactionType::all();
        $statuses = TransactionStatus::all();

        if ($users->count() < 2) {
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            $sender = $users->random();
            $receiver = $users->reject(function ($user) use ($sender) {
                return $user->id === $sender->id;
            })->random();

            Transaction::create([
                'income_user' => $receiver->id,
                'outcome_user' => $sender->id,
                'transaction_type_id' => $types->random()->id,
                'transaction_status_id' => $statuses->random()->id,
                'amount' => rand(100, 5000),
                'transaction_hash' => Str::random(32),
                'synced_at' => now(),
            ]);
        }
    }
}
