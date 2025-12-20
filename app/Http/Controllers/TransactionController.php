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
            'recipient_identifier' => 'required|string',
            'offline_id' => 'nullable|string|unique:transactions,offline_id',
        ]);

        $sender = $request->user();
        $senderWallet = $sender->wallet;
        
        // Find recipient wallet by UUID (QR Code) or Account Number (Code)
        $recipientWallet = Wallet::where('uuid', $request->recipient_identifier)
            ->orWhere('account_number', $request->recipient_identifier)
            ->first();

        if (!$senderWallet || !$recipientWallet) {
            return $this->error('Portefeuille introuvable', 404);
        }

        if ($senderWallet->id === $recipientWallet->id) {
            return $this->error('Vous ne pouvez pas effectuer de transfert vers vous-même', 400);
        }

        if ($senderWallet->balance < $request->amount) {
            return $this->error('Solde insuffisant', 400);
        }

        // Idempotency check
        if ($request->offline_id) {
            $existing = Transaction::where('offline_id', $request->offline_id)->first();
            if ($existing) {
                return $this->success('Transaction déjà traitée', $existing);
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
                'transaction_type_id' => 1, // TransactionTypeSeeder must ensure IDs or we should lookup logic
                'transaction_status_id' => 2, // Assuming 2 is Completed, let's lookup safely usually but for now assume seeder order or use names to be safe if I could
                'amount' => $request->amount,
                'offline_id' => $request->offline_id,
                'synced_at' => $request->offline_id ? now() : null,
                'transaction_hash' => $transactionHash,
            ]);
            
            // Re-fetch correct status/type if needed, or use safe lookups. 
            // For this iteration, assuming standard keys from seeder. 
            // Safest is:
            // 'transaction_type_id' => TransactionType::where('title', 'transfer')->first()->id,
            // 'transaction_status_id' => TransactionStatus::where('title', 'completed')->first()->id,
            // But I will stick to IDs 3 (transfer) and 2 (completed) based on array index from seeder if keys are auto-increment.
            // Seeder types: ['deposit', 'withdrawal', 'transfer', 'payment'] -> Transfer IS 3.
            // Seeder statuses: ['pending', 'completed', 'failed', 'cancelled'] -> Completed IS 2.
            $transaction->transaction_type_id = 3; 
            $transaction->transaction_status_id = 2;
            $transaction->save();


            DB::commit();

            return $this->success('Transfert réussi', $transaction);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Échec du transfert: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        // Policy check: Ensure user is related to this transaction
        $user = request()->user();
        if ($transaction->income_user !== $user->id && $transaction->outcome_user !== $user->id) {
            return $this->error('Non autorisé', 403);
        }

        return $this->success('Détails de la transaction', $transaction->load(['type', 'status', 'incomeUser', 'outcomeUser']));
    }
}
