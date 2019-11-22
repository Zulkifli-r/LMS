<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [
            'title' => $this->title,
            'description' => $this->description,
            'created_by' => new Users($this->user),
            'media' => new Media($this->getMedia(strtolower($this->type))->first())
        ];

        return $res;
    }
}
