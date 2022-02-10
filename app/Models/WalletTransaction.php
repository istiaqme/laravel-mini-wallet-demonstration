<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'from_wallet_id',
        'from_wallet_native_amount',
        'from_wallet_currency',
        'from_wallet_current_balance',
        'conversion_rate',
        'sent_at',
        'to_wallet_id',
        'to_wallet_converted_amount',
        'to_wallet_currency',
        'to_wallet_current_balance',
        'received_at',
        'purpose'
    ];
}