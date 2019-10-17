<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizeException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // return route('login');
            // return ['code' => 401, 'message' => 'unauthenticate'];
            throw new UnauthorizeException();
        }
    }
}
