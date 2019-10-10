<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use Notifiable, HasRoles, HasMediaTrait, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guard_name = 'api';

    public function classrooms()
    {
        return $this->belongsToMany('App\Classroom');
    }

    public function category()
    {
        return $this->morphToMany('App\Category', 'categorizable');
    }

    public function classroomUsers()
    {
        return $this->hasMany( 'App\ClassroomUser' );
    }

    public function getRoleAttribute()
    {
        return $this->roles->pluck('name');
    }

    public function generateAvatar()
    {
        $avatarPath = Storage::disk('public')->path('').'/'.'avatar-'.$this->id.'.png';
        \Avatar::create($this->name)->save($avatarPath,100);
        $this->addMedia($avatarPath )->toMediaCollection( 'avatar','public' );
    }
}
