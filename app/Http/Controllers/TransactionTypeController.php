<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use Illuminate\Http\Request;

class TransactionTypeController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = TransactionType::all();
        return $this->success('Transaction types retrieved', $types);
    }
}
