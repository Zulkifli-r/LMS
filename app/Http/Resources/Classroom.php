<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Classroom extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'name' => $this->name,
            'slug' => $this->slug,
            'classroom_type' => $this->class_type,
            'classroom_owner' => auth()->user()->id == $this->user->id
                                        ? 'you'
                                        : $this->user->name,
            // TODO :'classroom_user' =>
        ];
    }
}
