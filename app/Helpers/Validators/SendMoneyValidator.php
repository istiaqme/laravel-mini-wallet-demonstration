<?php

namespace App\Helpers\Validators;

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
    public static function validateUserData (object $request) : array
    {
        $proceed = true;
        // key exists - from_wallet_native_amount
        if(!$request->has('from_wallet_native_amount')){
            return [
                "proceed" => false,
                "msg" => "Amount is required."
            ];
        }
        // key exists - to_wallet_id
        if(!$request->has('to_wallet_id')){
            return [
                "proceed" => false,
                "msg" => "Target wallet is required."
            ];
        }
        // key exists - password
        if(!$request->has('purpose')){
            return [
                "proceed" => false,
                "msg" => "Purpose is required."
            ];
        }

        // Check Purpose
        if(TextualData::checkTextNoNumberOnlySpace($request->purpose) == "No"){
            return [
                "proceed" => false,
                "msg" => "Only letters and white space allowed for Purpose."
            ];
        }
        

        // Check target wallet or not with currency
        $toWalletCurrency = substr($request->to_wallet_id, -3);
        $targetWallets = User::where('wallet_id', $request->to_wallet_id)->where('currency', $toWalletCurrency)->get();
        if(count($targetWallets) != 1){
            return [
                "proceed" => false,
                "msg" => "Target wallet is an invalid wallet."
            ];
        }

        // Check balance
        if($request->from_wallet_native_amount * 100 > $request->current_balance){
            return [
                "proceed" => false,
                "msg" => "Insufficient Balance."
            ];
        }

        

        return [
            "proceed" => true,
            "msg" => ""
        ];
    }


}