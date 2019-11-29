<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizeException;
use Closure;

class ClassroomOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $classroom = \App\Classroom::getBySlug($request->slug);
        if (!auth('api')->user()->isClassroomOwner($classroom)) {
            throw new UnauthorizeException();
        }
        return $next($request);
    }
}
