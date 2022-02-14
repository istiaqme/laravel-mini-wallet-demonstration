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
        (new SendMoneyValidator())->validateUserData($request);

        $newTransaction = $this->walletServiceInterface->createTransaction([
            "from_wallet_id" => $request->header('walletid'),
            "from_wallet_native_amount" => $request->from_wallet_native_amount,
            "from_wallet_currency" => $request->currency,
            "to_wallet_id" => $request->to_wallet_id,
            "purpose" => $request->purpose,
        ]);

        return response()->json([
            'type' => 'Success',
            'msg' => "Send money is successful.",
            'data' => $newTransaction
        ], 200);
    }

    public function transactions (Request $request) {
        $transactions = $this->walletServiceInterface::loadTransactions($request->walletId);
        return response()->json([
            'type' => 'Success',
            'msg' => "List Loaded.",
            'data' => $transactions['data']
        ], 200);
    }

    public function usersWhoUsedMostConversions (Request $request) {
        $users = $this->walletServiceInterface::userUsedMostConversion();
        return response()->json([
            'type' => 'Success',
            'msg' => "List Loaded.",
            'data' => $users
        ], 200);
    }

    public function totalAmountConvertedByAUser (Request $request) {
        $result = $this->walletServiceInterface::totalAmountConvertedByAUser($request->userId);
        return response()->json([
            'type' => 'Success',
            'msg' => "List Loaded.",
            'data' => $result
        ], 200);
    }

    public function thirdHighestAmountofTransactionsByAUser (Request $request) {
        $result = $this->walletServiceInterface::thirdHighestAmountofTransactionsByAUser($request->userId);
        return response()->json([
            'type' => 'Success',
            'msg' => "List Loaded.",
            'data' => $result
        ], 200);
    }


    
}
