<?php

namespace App\Services;

use App\Models\Bill;
use Illuminate\Support\Facades\Http;

class MidtransSnapService
{
    public function createTransaction(Bill $bill, string $orderId): array
    {
        $response = Http::withBasicAuth(config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->post($this->getSnapTransactionUrl(), [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) round($bill->amount_snapshot ?? $bill->amount),
                ],
                'item_details' => [
                    [
                        'id' => $bill->payment_type_id,
                        'price' => (int) round($bill->amount_snapshot ?? $bill->amount),
                        'quantity' => 1,
                        'name' => $bill->payment_type_name_snapshot ?? $bill->paymentType?->name ?? 'School Payment',
                    ],
                ],
                'customer_details' => [
                    'first_name' => $bill->student?->user?->name ?? 'Student',
                    'phone' => $bill->student?->parent_phone_number,
                ],
                'callbacks' => [
                    'finish' => null,
                ],
            ]);

        $response->throw();

        return $response->json();
    }

    public function verifySignature(array $payload): bool
    {
        $signature = hash(
            'sha512',
            ($payload['order_id'] ?? '')
            . ($payload['status_code'] ?? '')
            . ($payload['gross_amount'] ?? '')
            . config('services.midtrans.server_key')
        );

        return hash_equals($signature, $payload['signature_key'] ?? '');
    }

    private function getSnapTransactionUrl(): string
    {
        if (config('services.midtrans.is_production')) {
            return 'https://app.midtrans.com/snap/v1/transactions';
        }

        return 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }
}
