<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes, UUID, HasFactory;

    protected $fillable = [
        'bill_id',
        'transaction_code',
        'gateway_reference',
        'snap_token',
        'snap_redirect_url',
        'amount_paid',
        'payment_method',
        'status',
        'paid_at',
        'expired_at',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('transaction_code', 'like', '%' . $search . '%')
                ->orWhere('gateway_reference', 'like', '%' . $search . '%')
                ->orWhere('payment_method', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%')
                ->orWhereHas('bill.student.user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                })
                ->orWhereHas('bill', function ($query) use ($search) {
                    $query->where('payment_type_name_snapshot', 'like', '%' . $search . '%');
                });
        });
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
