<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    protected $fillable = ['name','lesson_id','url'];

    public function lesson():BelongsTo
    {
        return $this->belongsTo(CourseLesson::class,'lesson_id','id');
    }

}
