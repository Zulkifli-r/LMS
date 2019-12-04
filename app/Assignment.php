<?php

namespace App;

use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User','created_by');
    }

    public function teachable()
    {
        return $this->hasOne('App\Teachable','teachable_id')->where('teachable_type', 'assignment');
    }

    public function save(array $options = [])
    {
        if (!$this->created_by) {
            $this->created_by = auth('api')->user()->id;
        }

        parent::save($options);
    }

    public function findOrNotFound($id)
    {
        $assignment = self::where('id', $id);
        if($assignment->first()){
            return $assignment->first();
        }

        throw new NotFoundException('Assignment');
    }
}
