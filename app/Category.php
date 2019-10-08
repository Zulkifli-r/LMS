<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use Sluggable, SoftDeletes;

    public function sluggable()
    {
        return [ 'slug' => [ 'source' => 'name' ] ];
    }

    public function parent()
    {
        return $this->belongsTo( 'App\Category', 'parent_id' );
    }

    public function children()
    {
        return $this->hasMany( 'App\Category', 'parent_id' );
    }
}
