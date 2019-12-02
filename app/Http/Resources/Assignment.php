<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Assignment extends JsonResource
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
            'id' => $this->assignment->id,
            'title' => $this->assignment->title,
            'description' => $this->assignment->description,
            'created_by' => new Users($this->user),
        ];
    }
}
