<?php

namespace App\Services;
use App\Interfaces\UserServiceInterface;
use App\Models\User;

class UserService implements UserServiceInterface
{
    
    /* 
        @creates a new user along with wallet info
        @params: array
        @return: array 
    */
    public function create (array $data) : array
    {
        try {
            $newUser = new User();
            $newUser->name = $data['name'];
            $newUser->email = $data['email'];
            $newUser->password = hash("sha512", $data['password']); 
            $newUser->currency = strtoupper($data['currency']);
            $newUser->current_balance = 0.00;
            $newUser->save();
            $newUser = $newUser;
            $newUser->wallet_id = $data['email'].'@'.$newUser->id.'@'.strtoupper($data['currency']);
            $newUser->save();

            return [
                "type" => "Success",
                "data" => $newUser
            ];
        }
        catch(\Exception $e){
            return [
                "type" => "Error",
                "data" => [
                    "msg" => $e
                ]
            ];
        }
    }


}