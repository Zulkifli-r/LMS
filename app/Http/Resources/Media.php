<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Media extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'collection' => $this->collection_name,
            'download_url' => $this->getUrl(),
            'model_type' => $this->model_type,
            'createda_t' => $this->created_at->toIso8601String(),
            'created_at_for_humans' => $this->created_at->diffForHumans(),
        ];
    }
}
