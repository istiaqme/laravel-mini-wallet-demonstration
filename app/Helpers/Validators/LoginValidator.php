<?php

namespace App\Helpers\Validators;

use App\Helpers\TextualData;

class LoginValidator
{
    /* 
        @checks provided user data is validated according to the system
        @params: object
        @return: array
    */
    public static function validateUserData (object $request) : array
    {
        $proceed = true;
        // key exists - email
        if(!$request->has('email')){
            return [
                "proceed" => false,
                "msg" => "Email is required."
            ];
        }
        // key exists - password
        if(!$request->has('password')){
            return [
                "proceed" => false,
                "msg" => "Password is required."
            ];
        }
        // Check Email
        if(TextualData::checkEmail($request->email) == "No"){
            return [
                "proceed" => false,
                "msg" => "Invalid email format."
            ];
        }

        return [
            "proceed" => true,
            "msg" => ""
        ];
    }


}