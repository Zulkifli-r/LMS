<?php

namespace App\Exceptions;

use Exception;

class UnauthorizeException extends Exception
{
    public function render($request)
    {
        $message = $this->message?','.$this->message:'';
        return apiResponse(401,null, 'Unauthorized action'. $message);
    }
}
