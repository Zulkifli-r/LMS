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
        return $this->hasOne('App\Teachable','teachable_id','id');
    }

    public static function getById($id){
        $quiz = self::where('id', $id);
        if ($quiz = $quiz->first()) {
            return $quiz;
        }

        throw new NotFoundException('Quiz');
    }

    public function questions()
    {
        return $this->belongsToMany( 'App\Question' )->withPivot('weight')->withTimestamps();
    }
}
