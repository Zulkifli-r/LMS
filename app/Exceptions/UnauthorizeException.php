<?php

namespace App\Exceptions;

use Exception;

class UnauthorizeException extends Exception
{
    public function render($request)
    {
        return apiResponse(401,null, 'Unauthorized action');
    }
}
