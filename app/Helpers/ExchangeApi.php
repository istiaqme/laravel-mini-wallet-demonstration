<?php

namespace App\Helpers;

class ExchangeApi 
{

    /* 
        @checks provided currency exists in the system or not
        @params: 
        @return: array
    */
    public function liveRates () : array
    {
        try {
            $ch = curl_init();
            $url = "https://openexchangerates.org/api/latest.json?app_id=bca17d3d749c4cbcb9f0613e47c0e674";
        
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 80);
            
            $response = curl_exec($ch);
                
            if(curl_error($ch)){
                return [
                    "type" => "Error",
                    "data" => [
                        "msg" => curl_error($ch)
                    ]
                ];
            }

            $inAssoc = json_decode($response, true);
                
            return [
                "type" => "Success",
                "data" => $inAssoc
            ];
            
            curl_close($ch);
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