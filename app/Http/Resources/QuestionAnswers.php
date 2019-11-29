<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswers extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        dd($this);
        $res = [
            'questionId' => $this->questionId,
            'score' => array_key_exists('score', $this) ? $this->score : 0 ,
            'answeredAt' => array_key_exists('answeredAt', $this) ?
                \Carbon\Carbon::parse( $this->answeredAt->date )->toIso8601String() : null,
        ];

        // if (array_key_exists('questionType', $this)) {
        //     switch( $this->questionType ) {
        //         case 'multiple-choice':
        //         case 'boolean':
        //             return array_merge( $res, [ 'answerId' => $this->answerId ] );
        //         case 'multiple-response':
        //         case 'fill-in':
        //             return array_merge( $res, [ 'answers' => $this->answers ] );
        //         case 'essay':
        //             return array_merge( $res, [ 'content' => $this->content ] );
        //     }
        // }

        return $res;
    }
}
