<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Classroom extends JsonResource
{
    protected $includes = [];

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
            'slug' => $this->slug,
            'tags' => Tag::collection($this->tags),
            'classroom_type' => $this->class_type,
            'classroom_owner' => new Users($this->user),
            'created_at' => $this->created_at
        ];

        if ($this->includes->contains('teachers')) {
           $res['teachers'] = ClassroomUser::collection($this->teachers);
        }

        if ($this->includes->contains('students')) {
            $res['students'] = ClassroomUser::collection($this->students);
        }

        return $res;


    }
}
