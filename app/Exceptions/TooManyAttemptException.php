<?php

namespace App\Exceptions;

use Exception;

class TooManyAttemptException extends Exception
{

    public function render($request)
    {
        return apiResponse(429,null, $this->message);
    }
}
