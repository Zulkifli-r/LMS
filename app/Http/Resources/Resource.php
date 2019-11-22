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
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'data' => $this->data,
            'created_by' => new Users($this->user),
            'media' => new Media($this->getMedia(strtolower($this->type))->first())
        ];

        return $res;
    }
}
