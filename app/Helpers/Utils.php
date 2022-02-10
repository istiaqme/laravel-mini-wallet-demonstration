<?php

namespace App\Helpers;

class Utils 
{
    
    /* 
        @checks provided currency exists in the system or not
        @params: string
        @return: string
    */
    public static function randomString (int $length = 6) : string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


}