<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionChoiceItem extends Model
{
    protected $fillable = ['choice_text', 'is_correct'];
}
