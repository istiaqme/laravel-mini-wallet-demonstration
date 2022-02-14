<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'wallet_id',
        'currency',
        'current_balance'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function senderTransactions () {
        return $this->hasMany(WalletTransaction::class, 'from_wallet_id', 'wallet_id');
    }

    public function receiverTransactions () {
        return $this->hasMany(WalletTransaction::class, 'to_wallet_id', 'wallet_id');
    }
}
