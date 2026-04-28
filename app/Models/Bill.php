<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes, UUID, HasFactory;

    protected $fillable = [
        'student_id',
        'payment_type_id',
        'amount',
        'amount_snapshot',
        'payment_type_name_snapshot',
        'billing_month',
        'billing_year',
        'due_date',
        'status',
        'paid_date',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('payment_type_name_snapshot', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%')
                ->orWhere('billing_month', 'like', '%' . $search . '%')
                ->orWhere('billing_year', 'like', '%' . $search . '%')
                ->orWhereHas('student.user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                })
                ->orWhereHas('paymentType', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
        });
    }

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
