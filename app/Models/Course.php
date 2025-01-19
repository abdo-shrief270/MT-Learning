<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Course extends Model
{
    protected $fillable =['title','image','description','branch_id','instructor_id','price','discount_type','discount_amount','active'];

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
}
