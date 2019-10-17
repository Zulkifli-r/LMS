<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public function render()
    {
        return apiResponse(400, null, $this->message);
    }
}
