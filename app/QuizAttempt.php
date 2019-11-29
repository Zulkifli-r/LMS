<?php

namespace App;

use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $dates = [
        'completed_at'
    ];

    public function teachableUser()
    {
        return $this->belongsTo( 'App\TeachableUser');
    }

    public static function getByTeachableUserId($teachableUserId){
        $quizAttempt = self::where('teachable_user_id', $teachableUserId);
        if ($quizAttempt = $quizAttempt->first()) {
            return $quizAttempt;
        }

        return (new self);
    }

    public function answer( Collection $answer , $gradingMethod )
    {
        $answers = collect( json_decode( $this->answers ) );
        $question = collect( json_decode( $this->questions ) )->where( 'id', $answer[ 'questionId' ] )->first();

        if ( !$question ) return $this;

        $answerMethod = 'answer' . studly_case( str_replace( '-', ' ', $question->type ) );
        $newAnswers = $this->$answerMethod( $answer, $question , $gradingMethod );

        $key = array_search($newAnswers['questionId'], array_column($answers->toArray(), 'questionId'));

        $answers[$key] = $newAnswers;

        $this->answers = $answers->toJson();
        $this->save();
        return $this;
    }

    public function answerBoolean( Collection $answer, $question , $gradingMethod )
    {
        return $this->answerMultipleChoice( $answer, $question , $gradingMethod);
    }

    public function answerMultipleChoice( Collection $answer, $question , $gradingMethod )
    {
        $validator = Validator::make( $answer->all(), [
            'questionId' => 'required|size:8',
            'answerId' => 'required|size:8',
        ] );
        if ( $validator->fails() ) return null;

        $choiceItems = collect( $question->choiceItems );

        $key = array_search( $answer[ 'answerId' ], array_column($choiceItems->toArray(), 'id'));

        return [
            'questionId' => $answer[ 'questionId' ],
            'questionType' => $question->type,
            'score' => $choiceItems[$key]->isCorrect ? ($gradingMethod === 'weighted' ? $question->weight : 1) : 0 ,
            'answerId' => $answer[ 'answerId' ],
            'answeredAt' => \Carbon\Carbon::now(),
        ];
    }

    public function answerMultipleResponse( Collection $answer, $question )
    {
        $validator = Validator::make( $answer->all(), [
            'questionId' => 'required|size:8',
            'answers' => 'required|array',
        ] );
        if ( $validator->fails() ) return null;

        return [
            'questionId' => $answer[ 'questionId' ],
            'questionType' => $question->type,
            'answers' => $answer[ 'answers' ],
            'answeredAt' => \Carbon\Carbon::now(),
        ];
    }

    public function answerFillIn( Collection $answer, $question )
    {
        return $this->answerMultipleResponse( $answer, $question );
    }

    public function answerEssay( Collection $answer, $question )
    {
        $validator = Validator::make( $answer->all(), [
            'questionId' => 'required|size:8',
            'content' => 'required|string',
        ] );
        if ( $validator->fails() ) return null;

        return [
            'questionId' => $answer[ 'questionId' ],
            'questionType' => $question->type,
            'content' => $answer[ 'content' ],
            'answeredAt' => \Carbon\Carbon::now(),
        ];
    }

    public function complete()
    {
        $this->completed_at = \Carbon\Carbon::now();
        $this->save();

        if ( $this->isGradeCalculatable() ) {
            $grade = $this->grades()->first() ?: new Grade;
            $grade->grading_method = 'auto';
            $grade->grade = Grade::calculate( $this );
            $grade->comments = '';
            $grade->completed_at = $this->completed_at;

            $this->grades()->save( $grade );
        }

        return $this;
    }

    public function isGradeCalculatable()
    {
        $questions = collect( json_decode( $this->questions ) );
        $calculatable = true;
        $questions->each( function ( $question ) use ( $calculatable )
        {
            if ( !in_array( $question->type, [ 'multiple-choice', 'boolean', 'multiple-response' ] ) )
                $calculatable = false;
        } );
        return $calculatable;
    }
}
