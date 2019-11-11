<?php

namespace App\Repositories;

use App\Http\Resources\Classes;

class ClassesRepository
{
    protected $classroom;

    public function index()
    {
        return Classes::make(auth('api')->user())->includes([
            'created' => true,
            'joined' => true,
            'paginate' => 5
        ]);
    }

    public function joined()
    {
        return Classes::make(auth('api')->user())->includes([
            // 'created' => true,
            'joined' => true,
            // 'paginate' => 5
        ]);
    }

    public function created()
    {
        return Classes::make(auth('api')->user())->includes([
            'created' => true,
            // 'joined' => true,
            // 'paginate' => 5
        ]);
    }
}
