<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\AuthServiceInterface;

use App\Helpers\Validators\LoginValidator;

class AuthController extends Controller
{
    private $authServiceInterface;

    public function __construct(AuthServiceInterface $authServiceInterface) {
        $this->authServiceInterface = $authServiceInterface;
    }

    public function login (Request $request) {
        $validation = LoginValidator::validateUserData($request);
        if(!$validation["proceed"]){
            return response()->json([
                'type' => 'Error',
                'msg' => $validation["msg"],
                'data' => null
            ], 200);
        }
        else {
            $checkLogin = $this->authServiceInterface::checkLogin([
                "email" => $request->email,
                "password" => $request->password,
                "ip" => $request->ip()
            ]);
            if($checkLogin['type'] == "Error"){
                return response()->json([
                    'type' => 'Error',
                    'msg' => $checkLogin['data']['msg'],
                    'data' => null
                ], 200);
            }
            else {
                return response()->json([
                    'type' => 'Success',
                    'msg' => "Succesfully Logged In.",
                    'data' => $checkLogin['data']
                ], 200);
            }  
        }
    }

    public function logout (Request $request) {
        // required data already validated by middleware
        $this->authServiceInterface->logout($request);
        return response()->json([
            'type' => 'Success',
            'msg' => "Succesfully Logged Out.",
            'data' => null
        ], 200);
    }
}
