<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{
    use LogsActivity;
    protected $fillable =['name','active'];
    public function courses():HasMany
    {
        return $this->hasMany(Course::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'active']);
    }
}
