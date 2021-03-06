<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttempt extends JsonResource
{

    public $includes;

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
        if ($this->id) {
            $res = [
                'id' => $this->id,
                'attempt' => $this->attempt,
                'grading_method' => $this->grading_method,
                'completed_at' => $this->completed_at ? $this->completed_at->toIso8601String() : null,
                'completed_at_for_humans' => $this->completed_at ? $this->completed_at->diffForHumans() : null,
                'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
                'created_at_for_humans' => $this->created_at ? $this->created_at->diffForHumans() : null,
            ];
            if ($this->includes->has('answers') ) {
                $res['answers'] = $this->answers();
            }

            if ($this->includes->has('grade')) {
                $res['grade'] = new Grade($this->grade);
            }

            if ($this->includes->has('questions')) {
                $res['questions'] = $this->questions();
            }

            if ($this->includes->has('progress')) {
                $res['progress'] = $this->progres();
            }

            return $res;
        }

        return null;
    }

    public function includes($includes = [])
    {
        $this->includes = collect($includes);
        return $this;
    }

    private function answers(){
        $answers = json_decode($this->answers);
        foreach ($answers as $key => $value) {
            $res[$key] = [
                'questionId' => $value->questionId,
                'score' => array_key_exists('score', $value) ? $value->score : 0 ,
                'answeredAt' => array_key_exists('answeredAt', $value) ?
                    \Carbon\Carbon::parse( $value->answeredAt )->toIso8601String() : null,
            ];

            if ($this->includes->has('hideScore')) {
                unset($res[$key]['score']);
            }

            if (array_key_exists('questionType', $value)) {
                switch( $value->questionType ) {
                    case 'multiple-choice':
                    case 'boolean':
                        $res[$key]['answerId']= $value->answerId;
                        break;
                    case 'multiple-response':
                    case 'fill-in':
                        $res[$key]['answers']= $value->answers;
                        break;
                    case 'essay':
                        $res[$key]['content']= $value->content;
                        break;
                }
            }
        }

        return $res;
    }

    private function questions(){

        $questions = json_decode($this->questions);
        foreach ($questions as $key => $value) {
            $res[$key] = [
                'id' => $value->id,
                'type' => $value->type,
                'typeLabel' => \Str::title( str_replace( '-', ' ', $value->type ) ),
                'scoringMethod' => \Str::studly( $value->scoringMethod ),
                'content' => $value->content,
            ];

            if (in_array($value->type, ['multiple-choice', 'multiple-response','boolean'])) {
                $res[$key]['choiceItems'] = $this->multiple_choice($value->choiceItems);
            }

            if ( isset( $value->answers ) )
                $base[ 'answerCount' ] = count(  $value->answers  );
            }
        return $res;
    }

    private function multiple_choice($choiceItems){
        $choice = [];
        foreach ($choiceItems as $key => $value) {
            $choice[$key]['id'] = $value->id;
            $choice[$key]['choiceText'] = $value->choiceText;
        }

        return $choice;
    }

    private function progres(){
        $answers = collect(json_decode($this->answers));
        $count = $answers->count();
        $answered = $answers->where('answeredAt','<>','')->count();
        return ($answered/$count) * 100;
    }
}
