<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class QuestionQuiz extends Pivot
{
    public function quiz()
    {
        return $this->belongsTo( 'App\Quiz' );
    }

    public function question()
    {
        return $this->belongsTo( 'App\Question' );
    }
}
