<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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

    public function selfClassroomUser()
    {
        return $this->hasOne( 'App\ClassroomUser' )->where( 'user_id', auth()->user()->id );
    }

    public function students()
    {
        return $this->hasMany( 'App\ClassroomUser' )->role( 'student' );
    }

    public function scopePublic($query)
    {
        return $query->where('class_type', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('class_type', 'private');
    }
}
