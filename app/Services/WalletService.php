<?php

namespace App\Services;
use App\Interfaces\WalletServiceInterface;
use App\Models\WalletTransaction;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\ExchangeApi;

class WalletService implements WalletServiceInterface
{
    
    /* 
        @creates new transaction
        @params: array
        @return: array
    */
    public static function createTransaction (array $data) : array
    {
        $targetWalletCurrency = substr($data['to_wallet_id'], -3);

        $exchangedData = self::convertCurrency($data['from_wallet_currency'], $targetWalletCurrency, $data['from_wallet_native_amount']);
        if(!$exchangedData['proceed']){
            return [
                "type" => "Error",
                "data" => null
            ];
        }else {
            $conversionRate = $exchangedData['data']['rate'];
            $convertedAmount = $exchangedData['data']['convertedAmount']; // x100
            $amountInBaseCurrency = $exchangedData['data']['amountInBaseCurrency']; // x100

            $fromWalletNativeAmountX100 = $data['from_wallet_native_amount'] * 100;

            $fromWalletBalanceBeforeTransaction = self::walletCurrentBalance($data['from_wallet_id'])['data'];
            $targetWalletBalanceBeforeTransaction = self::walletCurrentBalance($data['to_wallet_id'])['data'];

            $fromWalletCurrentBalance = $fromWalletBalanceBeforeTransaction - $fromWalletNativeAmountX100;
        
            $targetWalletCurrentBalance = $targetWalletBalanceBeforeTransaction + $convertedAmount;

            $newTransaction = new WalletTransaction();
            $newTransaction->from_wallet_id = $data['from_wallet_id'];
            $newTransaction->from_wallet_native_amount = $fromWalletNativeAmountX100;
            $newTransaction->from_wallet_currency = $data['from_wallet_currency'];
            $newTransaction->from_wallet_current_balance = $fromWalletCurrentBalance;
            $newTransaction->conversion_rate = $conversionRate;
            $newTransaction->sent_at = Carbon::now();
            $newTransaction->to_wallet_id = $data['to_wallet_id'];
            $newTransaction->to_wallet_converted_amount = $convertedAmount;
            $newTransaction->to_wallet_currency = $targetWalletCurrency;
            $newTransaction->to_wallet_current_balance = $targetWalletCurrentBalance;
            $newTransaction->received_at = Carbon::now();
            $newTransaction->purpose = $data['purpose'];
            $newTransaction->amount_in_base_currency = $amountInBaseCurrency;
            $newTransaction->save();
            
            // update the senders current balance
            self::updateCurrentBalance($data['from_wallet_id'],  $fromWalletCurrentBalance);
            self::updateCurrentBalance($data['to_wallet_id'],  $targetWalletCurrentBalance);

            return [
                "type" => "Success",
                "data" => $newTransaction
            ];
        }

        
    }

    /* 
        @updates current balance of the provided wallet
        @params: string, int
        @return: array
    */
    public static function updateCurrentBalance (string $walletId, int $currentBalance) : array
    {
        $users = User::where('wallet_id', $walletId)->first();
        $users->current_balance = $currentBalance;
        $users->save();

        return [
            "type" => "Success",
            "data" => $users
        ];
    }

    /* 
        @returns current wallet balance
        @params: string
        @return: array
    */
    public static function walletCurrentBalance (string $walletId) : array
    {
        $users = User::where('wallet_id', $walletId)->first();

        return [
            "type" => "Success",
            "data" => $users->current_balance
        ];
    }

    /* 
        @convert currency according to the provided data - x100
        @params: string, string, int
        @return: array
    */
    public static function convertCurrency (string $fromCurrency, string $targetCurrency, int $amount) : array
    {
        $apiRates = ExchangeApi::liveRates();
        $rates;
        if($apiRates["type"] == "Success"){
            $rates = $apiRates["data"]["rates"];
        }
        else {
            return [
                "proceed" => false,
                "data" => [
                    "msg" => "API Problem."
                ]
            ];
        }
    
        $amountOfFromCurrencyInUSD = $amount / $rates[$fromCurrency];
        $amountOfTargetCurrency = $amountOfFromCurrencyInUSD * $rates[$targetCurrency];
        $conversionRate = $amountOfTargetCurrency / $amount;

        return [
            "proceed" => true,
            "data" => [
                "rate" => number_format((float)$conversionRate, 2, '.', '') * 100,
                "convertedAmount" => number_format((float)$amountOfTargetCurrency, 2, '.', '') * 100,
                "amountInBaseCurrency" => $amountOfFromCurrencyInUSD * 100
            ]
        ];
    }


    /* 
        @convert currency according to the provided data - x100
        @params: string, string, int
        @return: array
    */
    public static function loadTransactions (string $walletId) : array
    {
        $data = WalletTransaction::where('from_wallet_id', $walletId)->orWhere('to_wallet_id', $walletId)->get();

        return [
            "type" => "Success",
            "data" => $data
        ];
    }




}