<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class ClassroomUser extends Pivot
{
    use HasRoles, SoftDeletes;

    protected $guard_name = 'api';

    protected $dates = ['last_accessed_at'];

    public function classroom()
    {
        return $this->belongsTo('App\Classroom');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public static function whereInMultiple(array $columns, $values)
    {
        $values = array_map(function (array $value) {
            return "('".implode($value, "', '")."')";
        }, $values);

        return static::query()->whereRaw(
            '('.implode($columns, ', ').') in ('.implode($values, ', ').')'
        );
    }
}
