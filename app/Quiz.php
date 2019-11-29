<?php

namespace App;

use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Quiz extends Model implements HasMedia
{
    use HasMediaTrait, SoftDeletes;

    protected $fillable = ['grading_method', 'title', 'description', 'time_limit', 'created_by'];



    public function user()
    {
        return $this->belongsTo('App\User','created_by');
    }

    public function save(array $options = [])
    {
        if (!$this->created_by) {
            $this->created_by = auth('api')->user()->id;
        }

        parent::save($options);
    }

    public function teachable()
    {
        return $this->hasOne('App\Teachable','teachable_id','id')->where('teachable_type','quizz');
    }

    public static function getById($id){
        $quiz = self::where('id', $id)->withTrashed();
        if ($quiz = $quiz->first()) {
            return $quiz;
        }

        throw new NotFoundException('Quiz');
    }

    public function questions()
    {
        return $this->belongsToMany( 'App\Question' )->withPivot('weight')->withTimestamps();
    }

    public function randomizeQuestions()
    {
        $questions = $this->questions()->with( 'choiceItems' )->get();

        $questions = $questions->map( function ( $question )
        {
            if ( $question->choiceItems->count() > 0 )
                $question->choiceItems = $question->choiceItems->shuffle();
            return $question;
        } );

        return $questions;
    }

    public static function prepareSnapshot( $questions)
    {
        $questionSnapshots = collect([]);
        $answerSnapshots = collect([]);


        $questions->each( function ( $question ) use ( $questionSnapshots, $answerSnapshots )
        {

            $questionArray = [
                'id' => \Str::random( 8 ),
                'type' => $question->question_type,
                'scoringMethod' => $question->scoring_method,
                'content' => $question->content,
            ];

            $answerArray = [
                'questionId' => $questionArray['id']
            ];
            // dd($question);

            if ( $question->choiceItems->count() > 0 ) {
                $questionArray[ 'choiceItems' ] = collect([]);
                $question->choiceItems->each( function ( $choiceItem ) use ( $questionArray ) {
                    $questionArray[ 'choiceItems' ]->push([
                        'id' => \Str::random( 8 ),
                        'choiceText' => $choiceItem->choice_text,
                        'isCorrect' => $choiceItem->is_correct,
                    ]);
                } );
            }

            if ( $question->answers != null )
                $questionArray[ 'answers' ] = $question->answers;

            $questionSnapshots->push( $questionArray );
            $answerSnapshots->push( $answerArray );
        } );

        return [
            "questions" => $questionSnapshots ,
            "answers" => $answerSnapshots
        ];
    }
}
