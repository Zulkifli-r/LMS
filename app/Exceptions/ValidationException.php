<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected $errors;

    public function __construct($errors) {
        $this->errors = $errors;
    }

    public function render()
    {
        return apiResponse(400, null, $this->errors);
    }
}
