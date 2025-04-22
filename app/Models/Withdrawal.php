<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'donation_id',
        'user_id',
        'amount',
        'solana_wallet',
        'withdrawn_at',
        'status', // Adicionando o status
        'approved_at', // Adicionando a data de aprovação
        'rejected_at', // Adicionando a data de rejeição
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
