<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Validators\SignupValidator;

use App\Interfaces\UserServiceInterface;



class UserController extends Controller
{
    private $userServiceInterface;

    public function __construct(UserServiceInterface $userServiceInterface) {
        $this->userServiceInterface = $userServiceInterface;
    }

    public function signup (Request $request ) {
        $validation = SignupValidator::validateUserData($request);
        if(!$validation["proceed"]){
            return response()->json([
                'type' => 'Error',
                'msg' => $validation["msg"],
                'data' => null
            ], 200);
        }
        else {
            $newUser = $this->userServiceInterface::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => $request->password,
                "currency" => $request->selectedCurrency
            ]);
            return response()->json([
                'type' => 'Success',
                'msg' => "New User Has Been Created",
                'data' => $newUser
            ], 200);
        }
        
    }
}
