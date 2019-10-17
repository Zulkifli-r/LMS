<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Permission\Traits\HasRoles;

class ClassroomUser extends Pivot
{
    use HasRoles;

    protected $guard_name = 'api';

    public function classroom()
    {
        return $this->belongsTo('App\Classroom');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
