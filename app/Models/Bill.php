<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\BillType;
use App\Models\Payment;
use App\Models\User;
use App\Models\BillAttachment;

class Bill extends Model
{
    use LogsActivity;
    protected $fillable =['title','bill_type_id','added_by','payment_id','amount','image','notes','active'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title','bill_type_id','added_by','payment_id','amount','image','notes','active']);
    }

    public function billType()
    {
        return $this->belongsTo(BillType::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    public function adder()
    {
        return $this->belongsTo(User::class,'added_by','id');
    }
}
