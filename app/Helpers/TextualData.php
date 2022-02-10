<?php

namespace App\Helpers;

class TextualData 
{
    

    /* 
        @checks if name only contains letters and whitespace
        @params: string
        @return: string
    */
    public static function checkName (string $name) : string
    {
        $validated = "Yes";
        if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
            $validated = "No";
        }
        return $validated;
    }

    /* 
        @checks if text only contains letters and whitespace
        @params: string
        @return: string
    */
    public static function checkTextNoNumberOnlySpace (string $text) : string
    {
        $validated = "Yes";
        if (!preg_match("/^[a-zA-Z-' ]*$/",$text)) {
            $validated = "No";
        }
        return $validated;
    }

    /* 
        @checks if e-mail address is well-formed
        @params: string
        @return: string
    */
    public static function checkEmail (string $email) : string
    {
        $validated = "Yes";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validated = "No";
        }
        return $validated;
    }


}