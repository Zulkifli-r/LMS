<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class TeachableUser extends Pivot implements HasMedia
{
    use HasMediaTrait;

    public function classroomUser()
    {
        return $this->belongsTo( 'App\ClassroomUser' );
    }

    public function media()
    {
        return $this->morphMany( 'Spatie\MediaLibrary\Models\Media', 'model' );
    }

    public function teachable()
    {
        return $this->belongsTo( 'App\Teachable' );
    }

    public function type()
    {
        return $this->teachable()->first()->teachable_type;
    }

    public function complete( $completedAt = null )
    {
        if ( $this->completed_at != null )
            return $this;

        $this->completed_at = $completedAt ? Carbon::parse( $completedAt ) : Carbon::now();
        $this->save();

        return $this;
    }

    public function quizAttempts()
    {
        return $this->hasMany( 'App\QuizAttempt', 'teachable_user_id' )->orderBy( 'attempt', 'desc' );
    }
}
