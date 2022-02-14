<?php

namespace App\Services;
use App\Exceptions\TransactionException;
use App\Interfaces\WalletServiceInterface;
use App\Models\WalletTransaction;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\ExchangeApi;

use Illuminate\Support\Facades\DB;

class WalletService implements WalletServiceInterface 
{
    /* 
        @creates new transaction
        @params: array
        @return: array
    */
    public function createTransaction (array $data) : object
    {
        $targetWalletCurrency = substr($data['to_wallet_id'], -3);

        $exchangedData = $this->convertCurrency($data['from_wallet_currency'], $targetWalletCurrency, $data['from_wallet_native_amount']);
        if(!$exchangedData['proceed']){
            throw new TransactionException("Transaction Failed.");
        }

        $conversionRate = $exchangedData['rate'];
        $convertedAmount = $exchangedData['convertedAmount']; // x100
        $amountInBaseCurrency = $exchangedData['amountInBaseCurrency']; // x100

        $fromWalletNativeAmountX100 = $data['from_wallet_native_amount'] * 100;

        $fromWalletBalanceBeforeTransaction = 
            $this->walletCurrentBalance($data['from_wallet_id'])['data'];
        $targetWalletBalanceBeforeTransaction = 
            $this->walletCurrentBalance($data['to_wallet_id'])['data'];

        $fromWalletCurrentBalance = 
            $fromWalletBalanceBeforeTransaction - $fromWalletNativeAmountX100;
    
        $targetWalletCurrentBalance = 
            $targetWalletBalanceBeforeTransaction + $convertedAmount; // converted amount returned with x100

        // Create New Transaction
        $newTransaction = $this->newTransaction ([
            'from_wallet_id' => $data['from_wallet_id'],
            'from_wallet_native_amount' => $fromWalletNativeAmountX100,
            'from_wallet_currency' => $data['from_wallet_currency'],
            'from_wallet_current_balance' => $fromWalletCurrentBalance,
            'conversion_rate' => $conversionRate,
            'to_wallet_id' => $data['to_wallet_id'],
            'converted_amount' => $convertedAmount,
            'target_wallet_currency' => $targetWalletCurrency,
            'target_wallet_current_balance' => $targetWalletCurrentBalance,
            'purpose' => $data['purpose'],
            'amount_in_base_currency' => $amountInBaseCurrency
        ]);

        // update the sender and recivers current balance and after that activate
        $this->updateCurrentBalance($data['from_wallet_id'],  $fromWalletCurrentBalance)
        &&
        $this->updateCurrentBalance($data['to_wallet_id'],  $targetWalletCurrentBalance)
            ? $this->activateTransaction($newTransaction->id) 
            : '';

        return $newTransaction;

        
    }

    public function newTransaction (array $data) : object {
        $newTransaction = new WalletTransaction();
        $newTransaction->from_wallet_id = $data['from_wallet_id'];
        $newTransaction->from_wallet_native_amount = $data['from_wallet_native_amount'];
        $newTransaction->from_wallet_currency = $data['from_wallet_currency'];
        $newTransaction->from_wallet_current_balance = $data['from_wallet_current_balance'];
        $newTransaction->conversion_rate = $data['conversion_rate'];
        $newTransaction->sent_at = Carbon::now();
        $newTransaction->to_wallet_id = $data['to_wallet_id'];
        $newTransaction->to_wallet_converted_amount =  $data['converted_amount'];
        $newTransaction->to_wallet_currency = $data['target_wallet_currency'];
        $newTransaction->to_wallet_current_balance = $data['target_wallet_current_balance'];
        $newTransaction->received_at = Carbon::now();
        $newTransaction->purpose = $data['purpose'];
        $newTransaction->amount_in_base_currency = $data['amount_in_base_currency'];
        $newTransaction->status = "Active";
        $newTransaction->save();

        return $newTransaction;
    } 

    /* 
        @updates current balance of the provided wallet
        @params: string, int
        @return: array
    */
    private function updateCurrentBalance (string $walletId, int $currentBalance) : bool
    {
        $user = User::where('wallet_id', $walletId)->first();

        if(!$user){
            return false;
        }

        $user->current_balance = $currentBalance;
        $user->save();

        return true;
    }

    /* 
        @returns current wallet balance
        @params: string
        @return: array
    */
    public function walletCurrentBalance (string $walletId) : array
    {
        $user = User::where('wallet_id', $walletId)->first();

        if(!$user){
            return [
                "type" => "Error",
                "data" => [
                    "msg" => "Wallet not found."
                ]
            ];
        }

        return [
            "type" => "Success",
            "data" => $user->current_balance
        ];
    }

    /* 
        @convert currency according to the provided data - x100
        @params: string, string, int
        @return: array
    */
    private function convertCurrency (string $fromCurrency, string $targetCurrency, int $amount) : array
    {
        $apiRates = (new ExchangeApi())->liveRates();
        // purposely done to rollback transaction
        if($apiRates["type"] !== "Success"){
            return [
                "proceed" => false,
            ];
        }

        $rates = $apiRates["data"]["rates"];
        $amountOfFromCurrencyInUSD = $amount / $rates[$fromCurrency];
        $amountOfTargetCurrency = $amountOfFromCurrencyInUSD * $rates[$targetCurrency];
        $conversionRate = $amountOfTargetCurrency / $amount;

        return [
            "proceed" => true,
            "rate" => number_format((float)$conversionRate, 2, '.', '') * 100,
            "convertedAmount" => number_format((float)$amountOfTargetCurrency, 2, '.', '') * 100,
            "amountInBaseCurrency" => $amountOfFromCurrencyInUSD * 100
        ];
    }

    /* 
        @Activate Transaction Row - sets status Active
        @params: string
        @return: array
    */
    private function activateTransaction (string $transactionId) : bool
    {
        $transaction = WalletTransaction::where('id', $transactionId)->first();

        if(!$transaction){
            return false;
        }

        $transaction->status = "Active";
        $transaction->save();

        return true;
    }


    
    public function loadTransactions (string $walletId) : array
    {
        $data = WalletTransaction::where('from_wallet_id', $walletId)->orWhere('to_wallet_id', $walletId)->get();

        return [
            "type" => "Success",
            "data" => $data
        ];
    }

    public function userUsedMostConversion() : object {
        $users = WalletTransaction::select('from_wallet_id', \DB::raw("count(id) as total_transactions"))->with('sender')->where('status', 'active')->groupBy('from_wallet_id')->get();
        return $users;
    }

    public function totalAmountConvertedByAUser(int $userId) : object {

        $user = User::with(['senderTransactions' => function($query){
            $query->select(\DB::raw("SUM(amount_in_base_currency) as total_amount, from_wallet_id"))->where('status', 'Active');
        }])->where('id', $userId)->first();
        return $user;
    }

    public function thirdHighestAmountofTransactionsByAUser(int $userId) : object {

        $subQuery = DB::table('wallet_transactions')
        ->selectRaw('amount_in_base_currency, from_wallet_id')
        ->orderBy('amount_in_base_currency', 'desc')
        ->limit(3)
        ->toSql();

        $transaction = DB::table(DB::raw('('.$subQuery.') as transactions'))
        ->selectRaw('users.id, users.name, users.email, amount_in_base_currency')
        ->join('users', 'users.wallet_id', '=', 'transactions.from_wallet_id')
        ->orderBy('amount_in_base_currency')
        ->limit(1)
        ->first();

        return $transaction;
    }


  



}