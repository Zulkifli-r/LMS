<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizeException;
use Closure;

class ClassroomStudent
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
        if (!auth('api')->user()->isClassroomStudent($classroom)) {
            throw new UnauthorizeException('You\'re not this classroom student');
        }
        return $next($request);
    }
}
