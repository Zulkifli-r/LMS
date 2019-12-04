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
        if ($this->resource instanceof \App\Resource){
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
        }else{
            $res['resource'] = [
                'id' => $this->source->id,
                'type' => $this->source->type,
                'title' => $this->source->title,
                'description' => $this->source->description,
                'data' => $this->source->data,
                'created_by' => new Users($this->source->user),
                'media' => new Media($this->source->getMedia(strtolower($this->source->type))->first())
            ];
            $res['teachable' ] = new Teachable($this);

            return $res;
        }

    }
}
