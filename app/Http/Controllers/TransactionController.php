<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ResponseController;

class TransactionController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Policy check
        if ($request->user()->cannot('viewAny', Transaction::class)) {
             return $this->error('Unauthorized', 403);
        }

        $transactions = Transaction::where('income_user', $user->id)
            ->orWhere('outcome_user', $user->id)
            ->with(['type', 'status'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success('Transactions retrieved', $transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'wallet_uuid' => 'required|uuid|exists:wallets,uuid',
            'offline_id' => 'nullable|string|unique:transactions,offline_id',
        ]);

        $sender = $request->user();
        $senderWallet = $sender->wallet;
        $recipientWallet = Wallet::where('uuid', $request->wallet_uuid)->first();

        if (!$senderWallet || !$recipientWallet) {
            return $this->error('Wallet not found', 404);
        }

        if ($senderWallet->id === $recipientWallet->id) {
            return $this->error('Cannot transfer to yourself', 400);
        }

        if ($senderWallet->balance < $request->amount) {
            return $this->error('Insufficient funds', 400);
        }

        // Idempotency check
        if ($request->offline_id) {
            $existing = Transaction::where('offline_id', $request->offline_id)->first();
            if ($existing) {
                return $this->success('Transaction already processed', $existing);
            }
        }

        // Create Transaction
        $transactionHash = hash('sha256', $sender->id . $recipientWallet->user_id . $request->amount . time());

        DB::beginTransaction();
        try {
            $senderWallet->withdraw($request->amount);
            $recipientWallet->deposit($request->amount);

            $transaction = Transaction::create([
                'income_user' => $recipientWallet->user_id,
                'outcome_user' => $sender->id,
                'transaction_type_id' => 1, // Assuming 1 is Transfer
                'transaction_status_id' => 1, // Assuming 1 is Completed
                'amount' => $request->amount,
                'offline_id' => $request->offline_id,
                'synced_at' => $request->offline_id ? now() : null,
                'transaction_hash' => $transactionHash,
            ]);

            DB::commit();

            return $this->success('Transfer successful', $transaction);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Transfer failed: ' . $e->getMessage(), 500);
        }
    }
}
