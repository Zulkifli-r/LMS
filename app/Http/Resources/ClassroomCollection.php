<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClassroomCollection extends ResourceCollection
{

    protected $includes;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function(Classroom $resource) use ($request){
            return $resource->includes($this->includes)->toArray($request);
        });
    }

    public function includes($includes = [])
    {
        $this->includes = collect($includes);
        return $this;
    }
}
