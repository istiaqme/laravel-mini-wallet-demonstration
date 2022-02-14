<?php

namespace App\Interfaces;

interface WalletServiceInterface 
{
    public function createTransaction(array $data) : object ;  
    public function loadTransactions(string $walletId) : array ; 
}