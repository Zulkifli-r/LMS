<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function render()
    {
        return apiResponse(404, null, $this->message.' not found');
    }
}
