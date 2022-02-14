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

        
        return response()->json([
            'type' => 'Success',
            'msg' => "Succesfully Logged In.",
            'data' => $checkLogin
        ], 200);



    }

    public function logout (Request $request) {
        // required data already validated by middleware
        $logout = $this->authServiceInterface->logout($request);

        return response()->json([
            'type' => 'Success',
            'msg' => "Succesfully Loggedout.",
            'data' => null
        ], 200);

    }

}
