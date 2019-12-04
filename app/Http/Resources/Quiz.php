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

        if ($this->resource instanceof \App\Quiz) {
            $res = [
                'id' => $this->id,
                'grading_method' => $this->gradingMethod,
                'title' => $this->title,
                'description' => $this->description,
                'time_limit' => $this->time_limit,
                'created_by' => new Users($this->user),
            ];
            if ($this->includes->has('questions')) {
                $res['questions'] = new QuestionCollection($this->questions);
            }
        } else{
            $res['quiz'] = [
                'id' => $this->quiz->id,
                'grading_method' => $this->quiz->gradingMethod,
                'title' => $this->quiz->title,
                'description' => $this->quiz->description,
                'time_limit' => $this->quiz->time_limit,
                'created_by' => new Users($this->quiz->user),
            ];

            $res['teachable'] =  new Teachable($this);

            if ($this->includes->has('questions')) {
                $res['quiz']['questions'] = new QuestionCollection($this->quiz->questions);
            }
        }

        return $res;
    }

    public function includes(array $includes = [])
    {
        $this->includes = collect($includes);
        return $this;
    }
}
