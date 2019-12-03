<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TeachableMediaCollection extends ResourceCollection
{
    // public $collect = 'App\Teachable';
    // public $collects = 'App\Teachable';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this);
        return [
            'data' => $this->collection
        ];
    }
}
