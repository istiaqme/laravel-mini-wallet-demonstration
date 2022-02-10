<?php

namespace App\Helpers;

class Currency 
{
    /* 
        list of currencies the system supports
    */
    public static array $validCurrencies =  [
        "BDT", "USD", "EUR"
    ];

    /* 
        @checks provided currency exists in the system or not
        @params: string
        @return: string
    */
    public static function checkExistence (string $currency) : string
    {
        if(in_array(strtoupper($currency), self::$validCurrencies)){
            return "Yes";
        }
        else {
            return "No";
        }
    }

    /* 
        @returns x/100 value
        @params: int
        @return: float
    */
    public static function toFloat (int $amount) : float
    {
        $toFloat = $amount/100;
        return number_format((float)$toFloat, 2, '.', '');
    }


}