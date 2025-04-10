<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashback extends Model
{
    // Definir os campos que podem ser atribuídos em massa
    protected $fillable = ['donation_id', 'amount', 'status', 'user_id', 'solana_wallet_address'];

}
