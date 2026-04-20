<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [
        'student_id',
        'payment_type_id',
        'amount',
        'due_date',
        'status',
        'year_start',
        'year_end',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
