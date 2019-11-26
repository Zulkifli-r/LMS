<?php

namespace App;

use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Resource extends Model implements HasMedia
{
    use HasMediaTrait, SoftDeletes;

    public function save(array $options = [])
    {
        if (!$this->created_by) {
            $this->created_by = auth('api')->user()->id;
        }

        parent::save($options);
    }

    public function teachable()
    {
        return $this->hasOne('\App\Teachable','teachable_id')->where('teachable_type', 'resource');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function getDataAttribute($value)
    {
        // dd($value);
        return $value?json_decode($value):null;
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }

    public static function getById($resource)
    {
        $resource = (new static)::where('id', $resource)->withTrashed();

        if (!$resource->first()) {
            throw new NotFoundException('Resource');
        }

        return $resource->first();
    }
}
