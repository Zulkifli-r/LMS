<?php

namespace App;

use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Question extends Model implements HasMedia
{
    use SoftDeletes,HasMediaTrait;

    protected $fillable = [
        'question_type', 'scoring_method', 'content', 'answers', 'created_by'
    ];

    protected $attributes = [
        'scoring_method' => 'default',
        'answers' => null
    ];

    public function save(array $options = [])
    {
        if (!$this->created_by) {
            $this->created_by = auth('api')->user()->id;
        }

        parent::save($options);
    }

    public function user()
    {
        return $this->belongsTo('\App\User', 'created_by');
    }

    public function choiceItems()
    {
        return $this->hasMany( 'App\QuestionChoiceItem' , 'question_id', 'id');
    }

    public function quizzes()
    {
        return $this->belongsToMany( 'App\Quiz' )->withPivot('weight')->withTimestamps();
    }

    public function setAnswersAttribute($value)
    {
        $this->attributes['answers'] = json_encode($value);
    }

    public function getAnswersAttribute($value)
    {
        return json_decode($value);
    }

    public static function getById($id){
        if ($question = static::where('id', $id)->first() ) {
            return $question;
        }

        throw new NotFoundException('Question');
    }
}
