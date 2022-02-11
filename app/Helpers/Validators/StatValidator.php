<?php

namespace App\Helpers\Validators;

use Illuminate\Http\Request;

use App\Helpers\TextualData;


class StatValidator
{
    public $request;

    public function __construct(Request $request){
        $this->request = $request;
        $this->validateUserData();
    }
    /* 
        @checks provided user data is validated according to the system
        @params: object
        @return: array
    */
    public function validateUserData ()
    {

        // key exists - email
        if(!$this->request->has('email')){
            return response()->json([
                'type' => 'Error',
                'msg' => "Email Not Found",
                'data' => null
            ], 200);
        }
        // key exists - password
        if(!$this->request->has('password')){
            return response()->json([
                'type' => 'Error',
                'msg' => "Password Not Found",
                'data' => null
            ], 200);
        }

        return $this;
    }


}