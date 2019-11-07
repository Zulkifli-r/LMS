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

    public function atocomplete($request)
    {
        $tag = $this->tag->containing($request->q);
        if ($request->has('limit')) {
            $tag->limit($request->limit);
        }

        return TagResource::collection($tag->get());
    }
}
