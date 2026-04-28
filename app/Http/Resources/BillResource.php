<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount_snapshot' => $this->amount_snapshot,
            'payment_type_name_snapshot' => $this->payment_type_name_snapshot,
            'billing_month' => $this->billing_month,
            'billing_year' => $this->billing_year,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'paid_date' => $this->paid_date,
            'student' => StudentResource::make($this->student),
            'paymentType' => PaymentTypeResource::make($this->paymentType),
        ];
    }
}
