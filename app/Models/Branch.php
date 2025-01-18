<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable =['name','active'];
    public function courses():HasMany
    {
        return $this->hasMany(Course::class);
    }
}
