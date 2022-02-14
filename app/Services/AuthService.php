<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use App\Exceptions\ServiceException;
use App\Exceptions\AuthException;
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
        
        $user = User::where('email', $data['email'])->get();
        if(!count($user) == 1){
            throw new ServiceException("Email Not Found Ex.");
        }
        
        // check password
        if(hash("sha512", $data['password']) != $user[0]->password) {
            throw new ServiceException("Password Mismatched.");
        }
        
        // create session token
        $newAuthToken = $this->createAuthToken([
            "user_id" => $user[0]->id,
            "ip" => $data['ip']
        ]);

        return [
            "name" => $user[0]->name,
            "email" => $user[0]->email,
            "user_id" => $user[0]->id,
            "wallet_id" => $user[0]->wallet_id,
            "auth_token" => $newAuthToken->token
        ];

    }

    /* 
        @creates auth token
        @params: array
        @return: object
    */
    private function createAuthToken (array $data) : object {
        $token = Uuid::uuid4()->toString();
        $newAuthToken = new AuthToken();
        $newAuthToken->ip_address = $data['ip'];
        $newAuthToken->user_id = $data['user_id'];
        $newAuthToken->token = $token;
        $newAuthToken->status = "Active";
        $newAuthToken->save();
        return $newAuthToken;
    }

    public function logout (object $request) : bool
    {
        $authToken = AuthToken::where('user_id', $request->header('userid'))
                ->where('token', $request->header('authtoken'))
                ->first();

        if($authToken){
            $authToken->status = "Inactive";
            $authToken->save();
            return true;
        }

        return true;
        
    }

    public function authMiddleware (array $data) : object
    {
        // wildcard check
        $authToken = AuthToken::where('token', $data['token'])
        ->where('user_id', $data['userId'])
        ->where('status', 'Active')->first();

        if(!$authToken) {
            throw new AuthException('Authorization Denied.');
        }
        
        // user and wallet get validated here
        $user = User::where('id', $data['userId'])
        ->where('wallet_id', $data['walletId'])
        ->first();

        if(!$user) {
            throw new AuthException('Authorization Denied with System Problem.');
        }

        return $user;
        
    }

}