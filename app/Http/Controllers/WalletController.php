<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;

class WalletController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWalletRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWalletRequest $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        //
    }
    /**
     * Get the current balance of the authenticated user's wallet.
     */
    public function balance(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return $this->error('Wallet not found', 404);
        }

        if ($request->user()->cannot('view', $wallet)) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success('Wallet balance retrieved', [
            'balance' => $wallet->balance,
            'currency' => 'FCFA'
        ]);
    }
}
