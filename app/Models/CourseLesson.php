<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseLesson extends Model
{
    protected $fillable =['title','image','description','course_id','video_link','active'];
    public function course():BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
