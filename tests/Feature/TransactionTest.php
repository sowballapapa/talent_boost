<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_transfer_money()
    {
        $sender = User::factory()->create();
        $sender->wallet()->create(['balance' => 1000, 'uuid' => 'sender-uuid']);

        $recipient = User::factory()->create();
        $recipient->wallet()->create(['balance' => 0, 'uuid' => 'recipient-uuid']);

        $response = $this->actingAs($sender)->postJson('/api/transactions/transfer', [
            'amount' => 100,
            'wallet_uuid' => 'recipient-uuid',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.amount', 100);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $sender->id,
            'balance' => 900,
        ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $recipient->id,
            'balance' => 100,
        ]);
    }

    public function test_offline_sync_idempotency()
    {
        $sender = User::factory()->create();
        $sender->wallet()->create(['balance' => 1000, 'uuid' => 'sender-uuid']);

        $recipient = User::factory()->create();
        $recipient->wallet()->create(['balance' => 0, 'uuid' => 'recipient-uuid']);

        $offlineId = 'offline-123';

        // First attempt
        $response1 = $this->actingAs($sender)->postJson('/api/transactions/transfer', [
            'amount' => 100,
            'wallet_uuid' => 'recipient-uuid',
            'offline_id' => $offlineId,
        ]);

        $response1->assertStatus(200);

        // Second attempt (should be idempotent)
        $response2 = $this->actingAs($sender)->postJson('/api/transactions/transfer', [
            'amount' => 100,
            'wallet_uuid' => 'recipient-uuid',
            'offline_id' => $offlineId,
        ]);

        $response2->assertStatus(200);

        // Balance should only change once
        $this->assertDatabaseHas('wallets', [
            'user_id' => $sender->id,
            'balance' => 900,
        ]);
    }
}
