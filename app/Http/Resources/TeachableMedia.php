<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeachableMedia extends JsonResource
{
    // public $collect = 'App\Teachable';
    // public $collects = 'App\Teachable';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $res = [
            'id' => $this->source->id,
            'type' => $this->source->type,
            'title' => $this->source->title,
            'description' => $this->source->description,
            'data' => $this->source->data,
            'created_by' => new Users($this->source->user),
            'media' => new Media($this->source->getMedia(strtolower($this->source->type))->first())
        ];

        return $res;
    }
}
