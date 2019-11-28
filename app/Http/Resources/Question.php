<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Question extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res['id'] = $this->id;
        $res['question_type'] = $this->question_type;
        $res['scoring_method'] = $this->scoring_method;
        $res['weight'] = $this->quizzes->first()->pivot->weight;
        $res['content'] = $this->content;
        $res['answers'] = $this->answers;
        $res['created_by'] = new Users($this->user);
        if (in_array($this->question_type, ['multiple-choice', 'boolean', 'multiple-response']) ) {
            foreach ($this->choiceItems as $key => $value) {
                $res['choices'][$key]['choice_text'] = $value['choice_text'];
                $res['choices'][$key]['is_correct'] = $value['is_correct'];
            }
        }
        return $res;
    }
}
