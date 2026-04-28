<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'transaction_code' => $this->transaction_code,
            'gateway_reference' => $this->gateway_reference,
            'snap_token' => $this->snap_token,
            'snap_redirect_url' => $this->snap_redirect_url,
            'amount_paid' => $this->amount_paid,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'paid_at' => $this->paid_at,
            'expired_at' => $this->expired_at,
            'bill' => BillResource::make($this->bill),
        ];
    }
}
