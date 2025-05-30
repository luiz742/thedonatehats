<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'expires_at',
        'wallet_id',
        'wallet_address',
        'shisha_price'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function withdrawal()
    {
        return $this->hasOne(Withdrawal::class);
    }

    public function wasWithdrawn()
    {
        return $this->withdrawal()->exists();
    }
}
