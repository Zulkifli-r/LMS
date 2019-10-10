<?php

namespace App\Exceptions;

use Exception;

class AuthorizationException extends Exception
{
    public function render($request)
    {
        return apiResponse(401,null,'This action is unauthorized');
    }
}
