<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BillAttachment extends Model
{
    use LogsActivity;
    protected $fillable =['value','bill_id','type_id','active'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['value','bill_id','type_id', 'active']);
    }
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
    public function type()
    {
        return $this->belongsTo(AttachmentType::class, 'type_id');
    }
}
