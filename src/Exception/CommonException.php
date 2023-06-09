<?php

namespace App\Exception;

class CommonException extends \Exception
{
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}