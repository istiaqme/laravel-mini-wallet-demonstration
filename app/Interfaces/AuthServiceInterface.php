<?php

namespace App\Interfaces;

interface AuthServiceInterface 
{
    public function checkLogin(array $data) : array ;
    public function logout(object $request) : bool ;
    public function authMiddleware(array $data) : object ;
}