<?php

namespace App;

use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Teachable extends Model implements HasMedia
{
    use HasMediaTrait, SoftDeletes;

    protected $dates = [
        'available_at', 'expires_at', 'deleted_at'
    ];

    protected $fillable = ['teachable_type', 'available_at', 'expires_at', 'pass_treshold', 'final_grade_weight', 'max_attempts_count'];

    public function classroom()
    {
        return $this->belongsTo( 'App\Classroom' );
    }

    public function teachableUsers()
    {
        return $this->hasMany( 'App\TeachableUser' );
    }

    public function teachableUser($classroomUser)
    {
        return $this->hasOne( 'App\TeachableUser' )->where('classroom_user_id', $classroomUser->id)->first();
    }

    public function assignment()
    {
        return $this->belongsTo('App\Assignment','teachable_id');
    }

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

    public function setExpiresAtAttribute($value)
    {
        return  $this->attributes['expires_at'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $value);
    }

    public function quiz()
    {
        return $this->belongsTo('App\Quiz','teachable_id');
    }

    public function resource()
    {
        return $this->belongsTo('App\Resource', 'teachable_id');
    }

    public function source()
    {
        return $this->belongsTo('App\Resource', 'teachable_id');
    }
}
