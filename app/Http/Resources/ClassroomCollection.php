<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClassroomCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return null;
        // return apiResponse(200, $this->collection);
        return [
            'data' => $this->collection,
        ];
        // return parent::toArray($request);
    }
}
