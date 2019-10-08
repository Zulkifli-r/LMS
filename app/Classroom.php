<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Classroom extends Model implements HasMedia
{
    use HasMediaTrait, Sluggable, SoftDeletes;

    // public function sluggable()
    // {
    //     # code...
    // }

    public function categories()
    {
        return $this->morphToMany( 'App\Category', 'categorizable' )->withTimestamps();
    }

    public function tag()
    {
        return $this->morphToMany('App\Tag', 'tagable')->withTimestamps();
    }

    public function classroomUsers()
    {
        return $this->hasMany( 'App\ClassroomUser' );
    }

    public function createdBy() {
        return $this->belongsTo( 'App\User', 'created_by' );
    }

    public function selfClassroomUser()
    {
        return $this->hasOne( 'App\ClassroomUser' )->where( 'user_id', auth()->user()->id );
    }

    public function students()
    {
        return $this->hasMany( 'App\ClassroomUser' )->role( 'student' );
    }
}
