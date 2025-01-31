<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseAttachment extends Model
{
    protected $fillable =['title','course_id','link','added_by'];


    public function adder()
    {
        return $this->belongsTo(User::class,'added_by','id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
