<?php

namespace App\Interfaces;

interface UserServiceInterface 
{
    public function create(array $data) : object ; 
}