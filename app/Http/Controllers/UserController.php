<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\SignupRequest;

use App\Interfaces\UserServiceInterface;



class UserController extends Controller
{
    private $userServiceInterface;

    public function __construct(UserServiceInterface $userServiceInterface) {
        $this->userServiceInterface = $userServiceInterface;
    }

    public function signup (SignupRequest $request ) {

        $newUser = $this->userServiceInterface->create([
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
