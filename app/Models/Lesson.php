<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lesson extends Model
{
    use LogsActivity;
    protected $fillable =['title','description','course_id','thumbnail','active','link','sort'];
    public function course():BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title','description','course_id','thumbnail','active','link','sort']);
    }
}
