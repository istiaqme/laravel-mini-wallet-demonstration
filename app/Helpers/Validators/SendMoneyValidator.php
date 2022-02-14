<?php

namespace App\Helpers\Validators;

use App\Exceptions\TransactionException;

use App\Helpers\Currency;
use App\Helpers\TextualData;
use App\Models\User;

class SendMoneyValidator 
{
    /* 
        @checks provided user data is validated according to the system
        @params: object
        @return: array
    */
    public function validateUserData (object $request) : bool
    {
        // key exists - from_wallet_native_amount
        if(!$request->has('from_wallet_native_amount')){
            throw new TransactionException("Sender's wallet is required.");
        }
        // key exists - to_wallet_id
        if(!$request->has('to_wallet_id')){
            throw new TransactionException("Receiver's wallet is required.");
        }
        // key exists - password
        if(!$request->has('purpose')){
            throw new TransactionException("Purpose is required.");
        }

        // Check Purpose
        if(TextualData::checkTextNoNumberOnlySpace($request->purpose) == "No"){
            throw new TransactionException("Only letters and white space allowed for Purpose.");
        }
        

        // Check target wallet or not with currency
        $toWalletCurrency = substr($request->to_wallet_id, -3);
        $targetWallets = User::where('wallet_id', $request->to_wallet_id)->where('currency', $toWalletCurrency)->get();
        if(count($targetWallets) != 1){
            throw new TransactionException("Target wallet is an invalid wallet.");
        }

        // Check balance
        if($request->from_wallet_native_amount * 100 > $request->current_balance){
            throw new TransactionException("Insufficient Balance.");
        }

        return true;
    }


}