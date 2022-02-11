<?php

namespace App\Services;

use App\Interfaces\AuthServiceInterface;

use App\Helpers\Utils;
use App\Models\User;
use App\Models\AuthToken;

class AuthService implements AuthServiceInterface 
{
    
    /* 
        @checks login and sets a auth token
        @params: array
        @return: array
    */
    public static function checkLogin (array $data) : array
    {
        $user = User::where('email', $data['email'])->get();
        if(!count($user) == 1){
            return [
                "type" => "Error",
                "data" => [
                    "msg" => "User does not exist."
                ]
            ];
        }
        else {
            // check password
            if(hash("sha512", $data['password']) != $user[0]->password) {
                return [
                    "type" => "Error",
                    "data" => [
                        "msg" => "Wrong Password."
                    ]
                ];
            }
            else {
                // create session token
                $newAuthToken = self::createAuthToken([
                    "user_id" => $user[0]->id,
                    "ip" => $data['ip']
                ]);

                return [
                    "type" => "Success",
                    "data" => [
                        "name" => $user[0]->name,
                        "email" => $user[0]->email,
                        "user_id" => $user[0]->id,
                        "wallet_id" => $user[0]->wallet_id,
                        "auth_token" => $newAuthToken['data']->token
                    ]
                    ];
            }
        }

        return [
            "type" => "Success",
            "data" => $newUser
        ];
    }

    /* 
        @creates auth token
        @params: array
        @return: array
    */
    public static function createAuthToken (array $data) : array {
        $token = Utils::randomString(18).$data['user_id'].mt_rand(1000, 10000).Utils::randomString(10);
        $newAuthToken = new AuthToken();
        $newAuthToken->ip_address = $data['ip'];
        $newAuthToken->user_id = $data['user_id'];
        $newAuthToken->token = $token;
        $newAuthToken->status = "Active";
        $newAuthToken->save();
        return [
            "type" => "Success",
            "data" => $newAuthToken
        ];
    }

    public static function logout (object $request) : array
    {
        try{
            $authToken = AuthToken::where('user_id', $request->header('userid'))
                ->where('token', $request->header('authtoken'))
                ->first();
            if($authToken){
                $authToken->status = "Inactive";
                $authToken->save();
                return [
                    "type" => "Success",
                    "data" => null
                ];
            }
            else {
                return [
                    "type" => "Success",
                    "data" => null
                ];
            }
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