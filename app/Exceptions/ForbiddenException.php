<?php

namespace App\Exceptions;

use Exception;

class ForbiddenException extends Exception
{
    public function render()
    {
        return apiResponse(403,null, $this->message);
    }
}
