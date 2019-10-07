<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tagable extends Model
{
    public function tag()
    {
        return $this->belongsTo('App\Tag');
    }
}
