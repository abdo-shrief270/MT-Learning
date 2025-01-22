<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CourseLesson extends Model
{
    protected $fillable =['title','image','description','course_id','thumbnail','active','link'];
    public function course():BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

//    protected function link(): Attribute
//    {
//        return Attribute::make(
//            get: function($value) {
//                if(env('FILESYSTEM_DISK')=='s3'){
//                    return $value?Storage::disk(env('FILESYSTEM_DISK'))->url($value):null;
//
//                }else{
//                    return $value?Storage::disk(env('FILESYSTEM_DISK'))->path($value):null;
//
//                }
//            }
//        );
//    }
}
