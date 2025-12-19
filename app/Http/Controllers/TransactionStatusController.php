<?php

namespace App\Http\Controllers;

use App\Models\TransactionStatus;
use Illuminate\Http\Request;

class TransactionStatusController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = TransactionStatus::all();
        return $this->success('Transaction statuses retrieved', $statuses);
    }
}
