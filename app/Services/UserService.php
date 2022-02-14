<?php

namespace App\Services;
use App\Exceptions\ServiceException;
use App\Interfaces\UserServiceInterface;
use App\Models\User;

class UserService implements UserServiceInterface
{
    
    /* 
        @creates a new user along with wallet info
        @param: array <data> :: associative
        @return: object 
    */
    public function create (array $data) : object
    {
        $newUser = new User();
        $newUser->name = $data['name'];
        $newUser->email = $data['email'];
        $newUser->password = hash("sha512", $data['password']); 
        $newUser->currency = strtoupper($data['currency']);
        $newUser->current_balance = 0;
        $newUser->save();
        $newUser = $newUser;
        $newUser->wallet_id = $data['email'].'@'.$newUser->id.'@'.strtoupper($data['currency']);
        $newUser->save();

        return $newUser;
    }


}