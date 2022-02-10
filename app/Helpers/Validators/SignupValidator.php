<?php

namespace App\Helpers\Validators;

use App\Helpers\Currency;
use App\Helpers\TextualData;
use App\Models\User;

class SignupValidator
{
    /* 
        @checks provided user data is validated according to the system
        @params: object
        @return: array
    */
    public static function validateUserData (object $request) : array
    {
        $proceed = true;
        // key exists - name
        if(!$request->has('name')){
            return [
                "proceed" => false,
                "msg" => "Name is required."
            ];
        }
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
        // key exists - selectedCurrency
        if(!$request->has('selectedCurrency')){
            return [
                "proceed" => false,
                "msg" => "Currency is required."
            ];
        }
        // Check Name
        if(TextualData::checkName($request->name) == "No"){
            return [
                "proceed" => false,
                "msg" => "Only letters and white space allowed for Name."
            ];
        }
        // Check Email
        if(TextualData::checkEmail($request->email) == "No"){
            return [
                "proceed" => false,
                "msg" => "Invalid email format."
            ];
        }
        // Check Selected Currency
        if(Currency::checkExistence($request->selectedCurrency) == "No"){
            return [
                "proceed" => false,
                "msg" => "Please select valid currency."
            ];
        }

        // Check user exists or not
        $user = User::where('email', $request->email)->get();
        if(count($user) != 0){
            return [
                "proceed" => false,
                "msg" => "User is already registered."
            ];
        }

        return [
            "proceed" => true,
            "msg" => ""
        ];
    }


}