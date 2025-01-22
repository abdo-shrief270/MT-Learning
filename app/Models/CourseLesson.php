<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CourseLesson extends Model
{
    use LogsActivity;
    protected $fillable =['title','image','description','course_id','thumbnail','active','link'];
    public function course():BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title','image','description','course_id','thumbnail','active','link']);
    }
}
