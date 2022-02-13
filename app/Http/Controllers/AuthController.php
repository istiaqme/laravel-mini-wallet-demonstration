<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\AuthServiceInterface;

use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    private $authServiceInterface;

    public function __construct(AuthServiceInterface $authServiceInterface) {
        $this->authServiceInterface = $authServiceInterface;
    }

    public function login (LoginRequest $request) {
        
        $checkLogin = $this->authServiceInterface->checkLogin([
            "email" => $request->email,
            "password" => $request->password,
            "ip" => $request->ip()
        ]);

        if($checkLogin['type'] === "Error"){
            return response()->json([
                'type' => 'Error',
                'msg' => $checkLogin['data']['msg'],
                'data' => null
            ], 200);
        }
        
        return response()->json([
            'type' => 'Success',
            'msg' => "Succesfully Logged In.",
            'data' => $checkLogin['data']
        ], 200);



    }

    public function logout (Request $request) {
        // required data already validated by middleware
        $logout = $this->authServiceInterface->logout($request);
        
        if($logout['type'] === "Error"){
            return response()->json([
                'type' => 'Error',
                'msg' => $logout['data']['msg'],
                'data' => null
            ], 200);
        }
        
        return response()->json([
            'type' => 'Success',
            'msg' => "Succesfully Loggedout.",
            'data' => $logout['data']
        ], 200);
    }
}
