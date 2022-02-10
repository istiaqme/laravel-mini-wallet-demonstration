<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Validators\SendMoneyValidator;
use App\Interfaces\WalletServiceInterface;



class Walletcontroller extends Controller
{
    private $walletServiceInterface;

    public function __construct(WalletServiceInterface $walletServiceInterface) {
        $this->walletServiceInterface = $walletServiceInterface;
    }

    public function sendMoney (Request $request) {
        $validation = SendMoneyValidator::validateUserData($request);
        if(!$validation["proceed"]){
            return response()->json([
                'type' => 'Error',
                'msg' => $validation["msg"],
                'data' => null
            ], 200);
        }
        else {
            $newTransaction = $this->walletServiceInterface::createTransaction([
                "from_wallet_id" => $request->header('walletid'),
                "from_wallet_native_amount" => $request->from_wallet_native_amount,
                "from_wallet_currency" => $request->currency,
                "to_wallet_id" => $request->to_wallet_id,
                "purpose" => $request->purpose,
            ]);
            if($newTransaction['type'] == "Success"){
                return response()->json([
                    'type' => 'Success',
                    'msg' => "Send money is successful.",
                    'data' => $newTransaction['data']
                ], 200);
            }
            else {
                return response()->json([
                    'type' => 'Error',
                    'msg' => "Network Problem.",
                    'data' => null
                ], 200);
            }
            
        }
    }

    public function transactions (Request $request) {
        $transactions = $this->walletServiceInterface::loadTransactions($request->walletId);
        return response()->json([
            'type' => 'Success',
            'msg' => "List Loaded.",
            'data' => $transactions['data']
        ], 200);
    }


    
}
