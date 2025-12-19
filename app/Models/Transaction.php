<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TransactionType;
use App\Models\TransactionStatus;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'income_user',
        'outcome_user',
        'transaction_type_id',
        'transaction_status_id',
        'amount',
        'offline_id',
        'synced_at',
        'transaction_hash'
    ];

    public function incomeUser()
    {
        return $this->belongsTo(User::class, 'income_user');
    }

    public function outcomeUser()
    {
        return $this->belongsTo(User::class, 'outcome_user');
    }

    public function type()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function status()
    {
        return $this->belongsTo(TransactionStatus::class, 'transaction_status_id');
    }
}
