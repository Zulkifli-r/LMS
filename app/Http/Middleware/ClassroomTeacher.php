<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizeException;
use Closure;

class ClassroomTeacher
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
        if (!auth('api')->user()->isClassroomTeacher($classroom)) {
            throw new UnauthorizeException('You\'re not this classroom teacher');
        }
        return $next($request);
    }
}
