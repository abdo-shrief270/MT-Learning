<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Course extends Model
{
    use LogsActivity;
    protected $fillable =['title','description','branch_id','instructor_id','price','discount_type','discount_amount','active','started_at','type','thumbnail','max_students'];

    public function branch():BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    public function instructor():BelongsTo
    {
        return $this->belongsTo(User::class,'instructor_id','id')->role('instructor');
    }
    public function lessons():HasMany
    {
        return $this->hasMany(CourseLesson::class);
    }
    public function days():HasMany
    {
        return $this->hasMany(CourseDay::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title','description','branch_id','instructor_id','price','discount_type','discount_amount','active','started_at','type','thumbnail','max_students']);
    }
}
