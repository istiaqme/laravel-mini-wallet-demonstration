<?php

namespace App\Interfaces;

interface WalletServiceInterface 
{
    public static function createTransaction(array $data) : array ; 
    public static function loadTransactions(string $walletId) : array ; 
}