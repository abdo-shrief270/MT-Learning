<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Enrollment extends Model
{
    use LogsActivity;
    protected $fillable =['student_id','course_id','bill_id','is_checked'];

    public function student():BelongsTo
    {
        return $this->belongsTo(User::class,'student_id','id')->role('Student');
    }
    public function course():BelongsTo
    {
        return $this->belongsTo(Course::class,'course_id','id');
    }
    public function bill():BelongsTo
    {
//        return $this->belongsTo(Bill::class,'bill_id','id')->whereHas('billType', function ($query) {
//            $query->where('type', 'in');
//        });
        return $this->belongsTo(Bill::class,'bill_id','id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['student_id','course_id','bill_id','is_checked']);
    }
}
