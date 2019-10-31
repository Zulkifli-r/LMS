<?php

namespace App\Repositories;

use App\Http\Resources\Tag as TagResource;
use Spatie\Tags\Tag;

class TagRepository
{
    protected $tag;

    public function __construct(Tag $tag) {
        $this->tag = $tag;
    }

    public function getListTags()
    {
        return TagResource::collection($this->tag->all());
    }

    public function atocomplete($query)
    {
        return TagResource::collection($this->tag->containing($query)->get());
    }
}
