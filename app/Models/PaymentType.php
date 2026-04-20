<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{
    use SoftDeletes, UUID;

    protected $table = [
        'name',
        'due_day',
        'amount',
        'is_recurring',
    ];

    public function bill()
    {
        return $this->hasMany(Bill::class);
    }
}
