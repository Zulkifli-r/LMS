<?php

namespace App\Repositories;

use App\Classroom;
use App\Http\Resources\Classroom as ClassroomResource;
use Illuminate\Http\Request;

class DiscoverRepository {

    protected $classroom;

    public function __construct(Classroom $classroom) {
        $this->classroom = $classroom;
    }

    public function byClassName(Request $request)
    {
        $classroom = $this->classroom->public();

        if ($request->has('page_limit')) {

        }

        if ($request->has('sort')) {
            # code...
        }


        $classroom = $classroom->where('name','LIKE','%'.$request->q.'%');

        return ClassroomResource::collection($classroom->paginate());
    }

    public function byTags(request $request)
    {
        $classroom = $this->classroom->public();

        $classroom = $classroom->withAnyTags($request->tags);

        return ClassroomResource::collection($classroom->paginate());
    }

}