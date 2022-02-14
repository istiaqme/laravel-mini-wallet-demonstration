<?php

namespace App\Interfaces;

interface WalletServiceInterface 
{
    public function createTransaction(array $data) : object ;  
    public function loadTransactions(string $walletId) : array ;
    public function userUsedMostConversion() : object;
    public function totalAmountConvertedByAUser(int $userId) : object;
    public function thirdHighestAmountofTransactionsByAUser(int $userId) : object;
}