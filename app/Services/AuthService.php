<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;

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
    public function checkLogin (array $data) : array
    {
        try {
            $user = User::where('email', $data['email'])->get();
            if(!count($user) == 1){
                return [
                    "type" => "Error",
                    "data" => [
                        "msg" => "User does not exist."
                    ]
                ];
            }
            
            // check password
            if(hash("sha512", $data['password']) != $user[0]->password) {
                return [
                    "type" => "Error",
                    "data" => [
                        "msg" => "Wrong Password."
                    ]
                ];
            }
            
            // create session token
            $newAuthToken = $this->createAuthToken([
                "user_id" => $user[0]->id,
                "ip" => $data['ip']
            ]);

            if($newAuthToken["type"] === "Error"){
                return [
                    "type" => "Error",
                    "data" => [
                        "msg" => $newAuthToken["data"]["msg"]
                    ]
                ];
            }

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
        catch(\Exception $e){
            return [
                "type" => "Error",
                "data" => [
                    "msg" => $e
                ]
            ];
        }
    }

    /* 
        @creates auth token
        @params: array
        @return: array
    */
    private function createAuthToken (array $data) : array {
        try {
            $token = Uuid::uuid4()->toString();
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
        catch(\Exception $e){
            return [
                "type" => "Success",
                "data" => [
                    "msg" => $e
                ]
            ];
        }
        
    }

    public function logout (object $request) : array
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

            return [
                "type" => "Success",
                "data" => null
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

    public function authMiddleware (array $data) : array
    {
        try{
            // wildcard check
            $authToken = AuthToken::where('token', $data['token'])
                ->where('user_id', $data['userId'])
                ->where('status', 'Active')->first();

            if(!$authToken) {
                return [
                    "type" => "Error",
                    "data" => [
                        "msg" => "User Not Found"
                    ]
                ];
            }
            
            // user and wallet get validated here
            $user = User::where('id', $data['userId'])
            ->where('wallet_id', $data['walletId'])
            ->first();

            if(!$user) {
                return [
                    "type" => "Error",
                    "data" => [
                        "msg" => "User Not Found"
                    ]
                ];
            }

            return [
                "type" => "Success",
                "data" => [
                    "user" => $user
                ]
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