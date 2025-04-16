<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'private_key',
        'public_key',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
