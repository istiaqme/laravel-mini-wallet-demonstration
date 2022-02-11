<?php

namespace App\Interfaces;

interface AuthServiceInterface 
{
    public static function checkLogin(array $data) : array ;
    public static function logout(object $request) : array ;
}