<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [
        'bill_id',
        'code',
        'payment_method',
        'payment_status',
        'total_amount',
        'payment_date',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
