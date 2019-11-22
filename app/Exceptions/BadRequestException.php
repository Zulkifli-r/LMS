<?php

namespace App\Exceptions;

use Exception;

class BadRequestException extends Exception
{
    public function render($request)
    {
        return apiResponse(500,null, $this->message);
    }
}

