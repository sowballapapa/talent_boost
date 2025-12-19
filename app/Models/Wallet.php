<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /** @use HasFactory<\Database\Factories\WalletFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'balance', 'uuid', 'account_number'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            if (empty($model->account_number)) {
                $model->account_number = self::generateAccountNumber();
            }
        });
    }

    public static function generateAccountNumber()
    {
        do {
            $number = mt_rand(1000000000, 9999999999); // 10 digit number
        } while (self::where('account_number', $number)->exists());

        return (string) $number;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deposit($amount)
    {
        $this->balance += $amount;
        $this->save();
    }

    public function withdraw($amount)
    {
        if ($this->balance < $amount) {
            return false;
        }
        $this->balance -= $amount;
        $this->save();
        return true;
    }
}
