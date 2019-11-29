<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionSnapshot extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [
            'id' => $this->questions->id,
            'type' => studly_case( $this->questions->type ),
            'typeLabel' => title_case( str_replace( '-', ' ', $this->questions->type ) ),
            'scoringMethod' => studly_case( $this->questions->scoringMethod ),
            'content' => clean( $this->questions->content ),
        ];
        if ( isset( $this->questions->answers ) )
            $base[ 'answerCount' ] = count( json_decode( $this->questions->answers ) );

        return $res;
    }
}
