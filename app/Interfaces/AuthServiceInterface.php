<?php

namespace App\Interfaces;

interface AuthServiceInterface 
{
    public function checkLogin(array $data) : array ;
    public function logout(object $request) : array ;
    public function authMiddleware(array $data) : array ;
}