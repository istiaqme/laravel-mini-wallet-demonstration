<?php

namespace App\Interfaces;

interface WalletServiceInterface 
{
    public function createTransaction(array $data) : array ; 
    public function loadTransactions(string $walletId) : array ; 
}