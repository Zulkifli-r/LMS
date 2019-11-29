<?php

namespace App;

use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizeException;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Tags\HasTags;

class Classroom extends Model implements HasMedia
{
    use HasMediaTrait, Sluggable, SoftDeletes, HasTags;

    protected $hidden = ['id'];

    protected $fillable = ['name','title','description','class_type','created_by'];

    public function sluggable()
    {
        return ['slug' => ['source' => 'name']];
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'classroom_user');
    }

    public function classroomUsers()
    {
        return $this->hasMany( 'App\ClassroomUser' );
    }


    public function createdBy() {
        return $this->belongsTo( 'App\User', 'created_by' );
    }

    public function classroomUser()
    {
        $user = Auth::guard('api')->user();

        return $this->hasOne( 'App\ClassroomUser' )->where( 'user_id', $user->id )->first();
    }

    public function students()
    {
        return $this->classroomUsers()->role('student');
    }

    public function isStudent()
    {
        return $this->students->pluck('user_id')->contains(auth('api')->user()->id);
    }

    public function teachers()
    {
        return $this->classroomUsers()->role('teacher');
    }

    public function teachables()
    {
        return $this->hasMany('App\Teachable');
    }

    public function scopePublic($query)
    {
        return $query->where('class_type', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('class_type', 'private');
    }

    public function isOwner() {
        return $this->user->id == auth('api')->user()->id;
    }

    public static function getBySlug($slug){
        $classroom = (new self)->where('slug', $slug);
        if ($classroom->first()) {
            return $classroom->first();
        }

        throw new NotFoundException('Class');
    }

    public function quizzes()
    {
        return $this->hasManyThrough(
            '\App\Quiz',
            '\App\Teachable',
            null,
            'id',
            null,
            'teachable_id'
        );
    }

    public function assigments()
    {
        return $this->hasManyThrough(
            '\App\Assignment',
            '\App\Teachable',
            null,
            'id',
            null,
            'teachable_id'
        );
    }

    public function resources()
    {
        return $this->hasManyThrough(
            '\App\Resource',
            '\App\Teachable',
            null,
            'id',
            null,
            'teachable_id'
        );
    }
}
