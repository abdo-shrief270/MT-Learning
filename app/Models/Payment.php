<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PaymentType;

class Payment extends Model
{
    use LogsActivity;
    protected $fillable =['title','payment_type_id','active'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title','payment_type_id', 'active']);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}
