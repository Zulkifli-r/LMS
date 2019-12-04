<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeachableQuiz extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this);
        try {
            //code...
            $res = [
                'id' => $this->quiz->id,
                'grading_method' => $this->quiz->gradingMethod,
                'title' => $this->quiz->title,
                'description' => $this->quiz->description,
                'time_limit' => $this->time_limit,
                'created_by' => new Users($this->user),
                'teachable' => new Teachable($this)
            ];

            return $res;
        } catch (\Throwable $th) {

        }

        // if ($this->includes->has('questions')) {
        //     $res['questions'] = Question::collection($this->questions);
        // }


    }
}
