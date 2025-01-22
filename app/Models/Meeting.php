<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Meeting extends Model
{
    use LogsActivity;
    protected $fillable = ['name','lesson_id','url'];

    public function lesson():BelongsTo
    {
        return $this->belongsTo(CourseLesson::class,'lesson_id','id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name','lesson_id','url']);
    }
}
