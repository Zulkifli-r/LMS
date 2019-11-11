<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class Classroom extends JsonResource
{
    public $includes = [];

    public function __construct($collect, $includes = []) {
        $this->includes = collect($includes);
        parent::__construct($collect);
    }

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
            'name' => $this->name,
            'description' => $this->description,
            'image' => new Media($this->getMedia('image')->first()),
            'slug' => $this->slug,
            'tags' => Tag::collection($this->tags),
            'classroom_type' => $this->class_type,
            'classroom_owner' => new Users($this->user),
            'created_at' => $this->created_at
        ];

        if ($this->includes->has('teachers')) {
           $res['teachers'] = ClassroomUser::collection($this->teachers);
        }

        if ($this->includes->has('students')) {
            $res['students'] = ClassroomUser::collection($this->students);
        }

        if ($this->includes->has('students_count')) {
            $res['students_count'] = $this->students->count();
        }

        if ($this->includes->has('created_class')) {

        }

        return $res;
    }

    public function includes($includes = [])
    {
        $this->includes = collect($includes);
        return $this;
    }

}
