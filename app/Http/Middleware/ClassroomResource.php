<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizeException;
use Closure;

class ClassroomResource
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
        if ( !$request->user('api') || !in_array($request->user('api')->id, $classroom->users->pluck('id')->toArray())  ) {
            throw new UnauthorizeException('You\'re not this classroom member');
        }

        return $next($request);
    }
}
