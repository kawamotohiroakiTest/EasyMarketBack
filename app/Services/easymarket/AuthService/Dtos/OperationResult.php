<?php

namespace App\Services\easymarket\AuthService\Dtos;

class OperationResult
{
    public bool $success;

    public function __construct(bool $success)
    {
        $this->success = $success;
    }
    
}