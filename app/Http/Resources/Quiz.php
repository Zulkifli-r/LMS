<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Quiz extends JsonResource
{

    protected $includes ;

    public function __construct($collect , $includes = []) {
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
            'id' => $this->id,
            'grading_method' => $this->gradingMethod,
            'title' => $this->title,
            'description' => $this->description,
            'time_limit' => $this->time_limit,
            'created_by' => new Users($this->user),
            'teachable' => new Teachable($this->teachable)
        ];

        if ($this->includes->has('questions')) {
            $res['questions'] = Question::collection($this->questions);
        }

        return $res;
    }

    public function includes(array $includes = [])
    {
        $this->includes = collect($includes);
        return $this;
    }
}
